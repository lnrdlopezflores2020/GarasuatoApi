<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\Codigo2FA;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    // Mostrar login
    public function loginForm()
    {
        return view('Cpanel.login');
    }

    // Procesar login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credenciales = [
            'correo' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credenciales)) {

            $user = Auth::user();

            // Crear código 2FA
            $codigo = rand(100000, 999999);

            $user->codigo_2fa = $codigo;
            $user->expira_2fa = Carbon::now()->addMinutes(5);
            $user->save();

            // Enviar correo
            Mail::to($user->correo)
            ->send(
                new Codigo2FA(
                    $user->nombre,
                    $codigo
                )
            );

            return redirect()->route('mostrar.2fa');
        }

        return back()->with('error', 'Correo o contraseña incorrectos');
    }

    // Mostrar formulario 2FA
    public function mostrar2FA()
    {
        return view('Cpanel.auth.verificar_2fa');
    }

    // Verificar código 2FA
    public function verificar2FA(Request $request)
    {
        $request->validate([
            'codigo' => 'required|max:6'
        ]);

        $user = Auth::user();

        // Verificar expiración
        if (Carbon::now()->greaterThan($user->expira_2fa)) {
            return back()->with('error', 'El código expiró, solicita uno nuevo.');
        }

        // Verificar código
        if ($request->codigo != $user->codigo_2fa) {
            return back()->with('error', 'El código es incorrecto.');
        }

        // Código válido → limpiar
        $user->codigo_2fa = null;
        $user->expira_2fa = null;
        $user->save();

        // Redirección según rol
        if ($user->ID_rol == 1) {
            // Administrador
            return redirect()->route('usuarios.index');
        } elseif ($user->ID_rol == 2) {
            // Cliente
            return redirect('/admon/inicio');
        } else {
            // Rol desconocido, cerrar sesión por seguridad
            Auth::logout();
            return redirect('/admon/login')->with('error', 'Rol no válido.');
        }
    }

    // Reenviar código 2FA
    public function reenviar2FA()
    {
        $user = Auth::user();

        $codigo = rand(100000, 999999);

        $user->codigo_2fa = $codigo;
        $user->expira_2fa = Carbon::now()->addMinutes(5);
        $user->save();

        Mail::to($user->correo)
                ->send(
                new Codigo2FA(
                    $user->nombre,
                    $codigo
                )
            );

        return back()->with('error', 'Se envió un nuevo código.');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admon/login');
    }

        // LOGIN API
    public function apiLogin(Request $request)
    {

        $request->validate([

            'correo' => 'required|email',
            'contrasena' => 'required'

        ]);

        $user = User::where(
            'correo',
            $request->correo
        )->first();

        if (
            !$user ||
            !Hash::check(
                $request->contrasena,
                $user->contrasena
            )
        ) {

            return response()->json([

                'success' => false,
                'message' => 'Credenciales incorrectas'

            ], 401);

        }

        // GENERAR CÓDIGO
        $codigo = rand(100000, 999999);

        $user->codigo_2fa = $codigo;

        $user->expira_2fa = Carbon::now()
            ->addMinutes(5);

        $user->save();

        // ENVIAR CORREO
        try {

            Mail::to($user->correo)
                ->send(
                    new Codigo2FA(
                        $user->nombre,
                        $codigo
                    )
                );

                } catch (\Exception $e) {

                    return response()->json([

                        'success' => false,
                        'message' => 'Error al enviar correo',
                        'error' => $e->getMessage()

                    ], 500);

                }

            // RESPUESTA
            return response()->json([

                'success' => true,
                'message' => 'Código enviado'

            ]);

    }
    public function apiVerificar2FA(Request $request)
    {

        $request->validate([

            'correo' => 'required|email',
            'codigo' => 'required'

        ]);

        $user = User::where(
            'correo',
            $request->correo
        )->first();

        if (!$user) {

            return response()->json([

                'success' => false,
                'message' => 'Usuario no encontrado'

            ], 404);

        }

        // verificar expiración
        if (Carbon::now()->greaterThan($user->expira_2fa)) {

            return response()->json([

                'success' => false,
                'message' => 'Código expirado'

            ], 401);

        }

        // verificar código
        if ($request->codigo != $user->codigo_2fa) {

            return response()->json([

                'success' => false,
                'message' => 'Código incorrecto'

            ], 401);

        }

        // limpiar código
        $user->codigo_2fa = null;
        $user->expira_2fa = null;
        $user->save();

        // crear token sanctum
        $token = $user->createToken(
            'garasuato-token'
        )->plainTextToken;

        return response()->json([

            'success' => true,

            'token' => $token,

            'usuario' => [

                'ID_usu' => $user->ID_usu,

                'nombre' => $user->nombre,

                'ape_pat' => $user->ape_pat,

                'ape_mat' => $user->ape_mat,

                'nom_comp' => $user->nom_comp,

                'correo' => $user->correo,

                'telefono' => $user->telefono,

                'estado' => $user->estado,

                'municipio' => $user->municipio,

                'CP' => $user->CP,

                'colonia' => $user->colonia,

                'calle' => $user->calle,

                'num_ext' => $user->num_ext,

                'num_int' => $user->num_int,

                'ID_rol' => $user->ID_rol,

                'foto_perfil' => $user->foto_perfil

            ]

        ]);

    }

    public function apiReenviar2FA(Request $request)
    {

        $request->validate([

            'correo' => 'required|email'

        ]);

        $user = User::where(
            'correo',
            $request->correo
        )->first();

        if (!$user) {

            return response()->json([

                'success' => false,
                'message' => 'Usuario no encontrado'

            ], 404);

        }

        // NUEVO CÓDIGO
        $codigo = rand(100000, 999999);

        $user->codigo_2fa = $codigo;

        $user->expira_2fa = Carbon::now()
            ->addMinutes(5);

        $user->save();

        // ENVIAR CORREO BONITO
        Mail::to($user->correo)
            ->send(
                new Codigo2FA(
                    $user->nombre,
                    $codigo
                )
            );

        return response()->json([

            'success' => true,
            'message' => 'Nuevo código enviado'

        ]);

    }

    public function apiRegister(Request $request)
    {

        $request->validate([

            'nombre' => 'required|max:50',
            'ape_pat' => 'required|max:50',
            'ape_mat' => 'required|max:50',

            'correo' => 'required|email|unique:usuario,correo',

            'contrasena' => [
                'required',
                'min:8'
            ],

            'telefono' => 'required|max:10',

            'estado' => 'required',
            'municipio' => 'required',
            'CP' => 'required',
            'colonia' => 'required',

            'calle' => 'required',
            'num_ext' => 'required',

        ]);

        $usuario = User::create([

            'nombre' => $request->nombre,

            'ape_pat' => $request->ape_pat,

            'ape_mat' => $request->ape_mat,

            'nom_comp' =>
                $request->nombre . ' ' .
                $request->ape_pat . ' ' .
                $request->ape_mat,

            'correo' => $request->correo,

            'contrasena' => Hash::make(
                $request->contrasena
            ),

            'telefono' => $request->telefono,

            'estado' => $request->estado,

            'municipio' => $request->municipio,

            'CP' => $request->CP,

            'colonia' => $request->colonia,

            'calle' => $request->calle,

            'num_ext' => $request->num_ext,

            'num_int' => $request->num_int,

            'ID_rol' => 2

        ]);

        return response()->json([

            'success' => true,

            'message' =>
                'Usuario registrado correctamente'

        ]);

    }


    public function buscarCP($cp)
    {
        try {

            $response = Http::timeout(20)
                ->get("https://sepomex.icalialabs.com/api/v1/zip_codes", [

                    'zip_code' => $cp

                ]);

            if (!$response->successful()) {

                return response()->json([

                    'ok' => false,
                    'message' => 'No se pudo consultar el CP'

                ], 500);

            }

            $data = $response->json();

            if (
                !isset($data['zip_codes']) ||
                count($data['zip_codes']) == 0
            ) {

                return response()->json([

                    'ok' => false,
                    'message' => 'CP no encontrado'

                ], 404);

            }

            $info = $data['zip_codes'][0];

            return response()->json([

                'ok' => true,

                'estado' => $info['d_estado'] ?? '',

                'municipio' => $info['d_mnpio'] ?? '',

                'colonias' => array_column(
                    $data['zip_codes'],
                    'd_asenta'
                )

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'ok' => false,
                'message' => $e->getMessage()

            ], 500);

        }
    }
   }
