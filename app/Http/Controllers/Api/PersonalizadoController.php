<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Personalizado;

class PersonalizadoController extends Controller
{

    public function guardar(Request $request)
    {

        try {

            $request->validate([

                'ID_prod' => 'required',
                'json_diseno' => 'required',
                'imagen_preview' => 'required'

            ]);

            $personalizado = Personalizado::create([

                'ID_usu' => auth()->user()->ID_usu,

                'ID_prod' => $request->ID_prod,

                'json_diseno' => $request->json_diseno,

                'imagen_preview' => $request->imagen_preview

            ]);

            return response()->json([

                'success' => true,

                'personalizado' => $personalizado

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'error' => $e->getMessage()

            ], 500);

        }

    }

}