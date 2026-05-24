<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PerfilController extends Controller
{
    public function index()
    {
        return view('cpanel.usuarios.perfil');
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:2048'
        ]);

        $user = Auth::user();

        // ✅ USAR LA PK REAL
        $nombre = 'user_' . $user->ID_usu . '.jpg';

        $ruta = $request->file('foto')->storeAs(
            'uploads/perfiles',
            $nombre,
            'public'
        );

        $user->foto_perfil = $ruta;
        $user->save();

        return back()->with('success', 'Foto actualizada');
    }


    public function actualizar(Request $request)
    {
        $user = Auth::user();

        if ($request->has('nombre')) {
            $user->nombre = $request->nombre;
        }

        if ($request->has('ape_pat')) {
            $user->ape_pat = $request->ape_pat;
        }

        if ($request->has('ape_mat')) {
            $user->ape_mat = $request->ape_mat;
        }

        if ($request->has('telefono')) {
            $user->telefono = $request->telefono;
        }

        // Nombre completo automático
        $user->nom_comp = trim(
            $user->nombre . ' ' . $user->ape_pat . ' ' . $user->ape_mat
        );

        $user->save();

        return back()->with('success', 'Dato actualizado correctamente');
    }

public function apiPerfil(Request $request)
{

    $user = $request->user();

    return response()->json([

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

public function apiActualizarPerfil(Request $request)
{

    $user = $request->user();

    $request->validate([

        'nombre' => 'required|max:50',
        'ape_pat' => 'required|max:50',
        'ape_mat' => 'required|max:50',
        'telefono' => 'required|max:10'

    ]);

    $user->nombre = $request->nombre;
    $user->ape_pat = $request->ape_pat;
    $user->ape_mat = $request->ape_mat;
    $user->telefono = $request->telefono;

    $user->nom_comp = trim(

        $request->nombre . ' ' .
        $request->ape_pat . ' ' .
        $request->ape_mat

    );

    $user->save();

    return response()->json([

        'success' => true,
        'message' => 'Perfil actualizado'

    ]);

}

public function apiActualizarFoto(Request $request)
{

    $request->validate([

        'foto' => 'required|image|max:2048'

    ]);

    $user = $request->user();

    $upload = cloudinary()
        ->upload(
            $request->file('foto')->getRealPath(),
            [
                'folder' => 'garasuato/perfiles',
                'public_id' => 'user_' . $user->ID_usu,
                'overwrite' => true
            ]
        );

    $url = $upload->getSecurePath();

    $user->foto_perfil = $url;

    $user->save();

    return response()->json([

        'foto' => $url

    ]);

}


public function actualizarTelefono(Request $request)
{

    $request->validate([

        'telefono' => 'required|max:10'

    ]);

    $user = Auth::user();

    $user->telefono =
        $request->telefono;

    $user->save();

    return response()->json([

        'success' => true

    ]);

}


}
