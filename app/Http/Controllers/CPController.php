<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CPController extends Controller
{

    public function buscarCP($cp)
    {

        try {

            $response = Http::timeout(20)
                ->get(
                    "https://sepomex.icalialabs.com/api/v1/zip_codes?zip_code=$cp"
                );

            $data = $response->json();

            if(
                !$data ||
                !isset($data['zip_codes'][0])
            ){

                return response()->json([

                    'ok' => false,
                    'message' => 'CP no encontrado'

                ]);

            }

            $info = $data['zip_codes'][0];

            $colonias = [];

            foreach($data['zip_codes'] as $item){

                $colonias[] =
                    $item['d_asenta'];

            }

            return response()->json([

                'ok' => true,

                'estado' =>
                    $info['d_estado'],

                'municipio' =>
                    $info['d_mnpio'],

                'colonias' =>
                    $colonias

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'ok' => false,
                'message' => $e->getMessage()

            ], 500);

        }

    }
}