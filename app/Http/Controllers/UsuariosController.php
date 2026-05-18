<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    public function index()
    {
        $usuarios = DB::table('usuario')->get();
        return view('cpanel.usuarios.indexusuarios', ['data' => $usuarios]);
    }

    public function create()
    {
        return view('cpanel.usuarios.createusuarios');
    }

    public function store(Request $request)
    {
        // Validación de campos obligatorios y formato
        $request->validate([
            'nombre'     => 'required|string|max:30',
            'ape_pat'    => 'required|string|max:30',
            'ape_mat'    => 'required|string|max:30',
            'correo'     => 'required|email|unique:usuario,correo',
            'contrasena' => 'required|string|min:8',
            'telefono'   => 'required|string|max:10',
            'calle'      => 'required|string|max:100',
            'num_ext'    => 'required|string|max:5',
            'num_int'    => 'nullable|string|max:5',
            'colonia'    => 'required|string|max:100',
            'municipio'  => 'required|string|max:100',
            'estado'     => 'required|string|max:100',
            'CP'         => 'required|string|max:10',

            
        ]);

        DB::table('usuario')->insert([
            'nombre'     => $request->nombre,
            'ape_pat'    => $request->ape_pat,
            'ape_mat'    => $request->ape_mat,
            'correo'     => $request->correo,
            'contrasena' => bcrypt($request->contrasena),  // Encriptar contraseña
            'telefono'   => $request->telefono,
            'calle'      => $request->calle,
            'num_ext'    => $request->num_ext,
            'num_int'    => $request->num_int,
            'colonia'    => $request->colonia,
            'municipio'  => $request->municipio,
            'estado'     => $request->estado,
            'CP'         => $request->CP,
        ]);

        return redirect()->route('usuarios.index');
    }

    public function destroy($id_usuario)
    {
        DB::table('usuario')->where('ID_usu', $id_usuario)->delete();
        return redirect()->route('usuarios.index');
    }

    public function edit($id_usuario)
    {
        $fila = DB::table('usuario')->where('ID_usu', $id_usuario)->first();
        return view('cpanel.usuarios.editusuarios', ['fila' => $fila]);
    }

    public function update(Request $request, $id_usuario)
    {
        // Validación igual que en store
        $request->validate([
            'nombre'     => 'required|string|max:30',
            'ape_pat'    => 'required|string|max:30',
            'ape_mat'    => 'required|string|max:30',
            'correo'     => "required|email|unique:usuario,correo,{$id_usuario},ID_usu",
            'contrasena' => 'nullable|string|min:8',  // Opcional en edición
            'telefono'   => 'required|string|max:10',
            'calle'      => 'required|string|max:100',
            'num_ext'    => 'required|string|max:5',
            'num_int'    => 'nullable|string|max:5',
            'colonia'    => 'required|string|max:100',
            'municipio'  => 'required|string|max:100',
            'estado'     => 'required|string|max:100',
            'CP'         => 'required|string|max:10',
        ]);

        $datosUpdate = [
            'nombre'    => $request->nombre,
            'ape_pat'   => $request->ape_pat,
            'ape_mat'   => $request->ape_mat,
            'correo'    => $request->correo,
            'telefono'  => $request->telefono,
            'calle'     => $request->calle,
            'num_ext'   => $request->num_ext,
            'num_int'   => $request->num_int,
            'colonia'   => $request->colonia,
            'municipio' => $request->municipio,
            'estado'    => $request->estado,
            'CP'        => $request->CP,
        ];

        // Solo actualizamos contraseña si se envió y no está vacía
        if ($request->filled('contrasena')) {
            $datosUpdate['contrasena'] = bcrypt($request->contrasena);
        }

        DB::table('usuario')->where('ID_usu', $id_usuario)->update($datosUpdate);

        return redirect()->route('usuarios.index');
    }

    public function apiIndex()
    {
        $usuarios = DB::table('usuario')->get();

        return response()->json([
            'usuarios' => $usuarios
        ]);
    }

    public function apiShow($id)
    {
        $usuario = DB::table('usuario')
            ->where('ID_usu', $id)
            ->first();

        return response()->json([
            'usuario' => $usuario
        ]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'nombre'     => 'required|string|max:30',
            'ape_pat'    => 'required|string|max:30',
            'ape_mat'    => 'required|string|max:30',
            'correo'     => 'required|email|unique:usuario,correo',
            'contrasena' => 'required|string|min:8',
            'telefono'   => 'required|string|max:10',
            'calle'      => 'required|string|max:100',
            'num_ext'    => 'required|string|max:5',
            'num_int'    => 'nullable|string|max:5',
            'colonia'    => 'required|string|max:100',
            'municipio'  => 'required|string|max:100',
            'estado'     => 'required|string|max:100',
            'CP'         => 'required|string|max:10',
            'ID_rol'     => 'required|integer',
        ]);

        DB::table('usuario')->insert([
            'nombre'     => $request->nombre,
            'ape_pat'    => $request->ape_pat,
            'ape_mat'    => $request->ape_mat,
            'nom_comp'   => $request->nombre . ' ' . $request->ape_pat . ' ' . $request->ape_mat,
            'correo'     => $request->correo,
            'contrasena' => bcrypt($request->contrasena),
            'telefono'   => $request->telefono,
            'calle'      => $request->calle,
            'num_ext'    => $request->num_ext,
            'num_int'    => $request->num_int,
            'colonia'    => $request->colonia,
            'municipio'  => $request->municipio,
            'estado'     => $request->estado,
            'CP'         => $request->CP,
            'ID_rol'     => $request->ID_rol,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente'
        ]);
    }

    public function apiUpdate(Request $request, $id)
    {
        $request->validate([
            'nombre'     => 'required|string|max:30',
            'ape_pat'    => 'required|string|max:30',
            'ape_mat'    => 'required|string|max:30',
            'correo'     => "required|email|unique:usuario,correo,{$id},ID_usu",
            'contrasena' => 'nullable|string|min:8',
            'telefono'   => 'required|string|max:10',
            'calle'      => 'required|string|max:100',
            'num_ext'    => 'required|string|max:5',
            'num_int'    => 'nullable|string|max:5',
            'colonia'    => 'required|string|max:100',
            'municipio'  => 'required|string|max:100',
            'estado'     => 'required|string|max:100',
            'CP'         => 'required|string|max:10',
            'ID_rol'     => 'required|integer',
        ]);

        $datosUpdate = [
            'nombre'    => $request->nombre,
            'ape_pat'   => $request->ape_pat,
            'ape_mat'   => $request->ape_mat,
            'nom_comp'  => $request->nombre . ' ' . $request->ape_pat . ' ' . $request->ape_mat,
            'correo'    => $request->correo,
            'telefono'  => $request->telefono,
            'calle'     => $request->calle,
            'num_ext'   => $request->num_ext,
            'num_int'   => $request->num_int,
            'colonia'   => $request->colonia,
            'municipio' => $request->municipio,
            'estado'    => $request->estado,
            'CP'        => $request->CP,
            'ID_rol'    => $request->ID_rol,
        ];

        if ($request->filled('contrasena')) {
            $datosUpdate['contrasena'] = bcrypt($request->contrasena);
        }

        DB::table('usuario')
            ->where('ID_usu', $id)
            ->update($datosUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente'
        ]);
    }

    public function apiDestroy($id)
    {
        DB::table('usuario')
            ->where('ID_usu', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}
