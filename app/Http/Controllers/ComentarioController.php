<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use Illuminate\Support\Facades\Auth;


class ComentarioController extends Controller
{
    public function store(Request $request)
    {
        try {

            $comentario = Comentario::create([
                'ID_prod' => $request->ID_prod,
                'ID_usu' => auth()->user()->ID_usu,
                'comentario' => $request->comentario,
                'calificacion' => $request->calificacion,
                'fecha' => now()
            ]);

            return response()->json([
                'usuario' => auth()->user()->nombre,
                'comentario' => $comentario->comentario,
                'calificacion' => $comentario->calificacion
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);

        }
    }

    public function destroy($id)
    {
        $usuario = Auth::user();

        // SOLO ADMIN
        if($usuario->ID_rol != 1){

            return response()->json([
                'success' => false
            ], 403);

        }

        Comentario::where(
            'ID_com',
            $id
        )->delete();

        return response()->json([

            'success' => true

        ]);
    }

        // API para crear comentario 
        public function apiStore(Request $request)
        {

            $comentario = Comentario::create([

                'ID_prod' => $request->ID_prod,

                'ID_usu' => auth()->user()->ID_usu,

                'comentario' => $request->comentario,

                'calificacion' => $request->calificacion,

            ]);

            return response()->json([

                'usuario' => auth()->user()->nombre,

                'comentario' => $comentario->comentario,

                'calificacion' => $comentario->calificacion

            ]);

        }

        
}