<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function generar()
    {
        try {

            $folder = storage_path('app/backups');

            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
            }

            $filename = 'backup_' . date('Y_m_d_H_i_s') . '.sql';

            $path = $folder . '/' . $filename;

            $database = DB::connection()->getDatabaseName();

            $sql = "-- Respaldo de base de datos GARASUATO\n";
            $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Base de datos: " . $database . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $tablas = DB::select('SHOW TABLES');

            $columnaTabla = 'Tables_in_' . $database;

            foreach ($tablas as $tabla) {

                $nombreTabla =
                    $tabla->$columnaTabla;

                $createTable = DB::select(
                    "SHOW CREATE TABLE `$nombreTabla`"
                );

                $create = $createTable[0]->{'Create Table'};

                $sql .= "DROP TABLE IF EXISTS `$nombreTabla`;\n";
                $sql .= $create . ";\n\n";

                $registros = DB::table($nombreTabla)->get();

                foreach ($registros as $registro) {

                    $datos =
                        (array) $registro;

                    $columnas =
                        array_map(
                            fn($columna) => "`$columna`",
                            array_keys($datos)
                        );

                    $valores =
                        array_map(function ($valor) {

                            if (is_null($valor)) {
                                return 'NULL';
                            }

                            return DB::getPdo()->quote($valor);

                        }, array_values($datos));

                    $sql .= "INSERT INTO `$nombreTabla` (";
                    $sql .= implode(', ', $columnas);
                    $sql .= ") VALUES (";
                    $sql .= implode(', ', $valores);
                    $sql .= ");\n";

                }

                $sql .= "\n";

            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            File::put($path, $sql);

            return response()->json([
                'success' => true,
                'message' => 'Respaldo generado correctamente',
                'archivo' => $filename
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'No se pudo generar el respaldo',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function descargar($archivo)
    {
        $path = storage_path('app/backups/' . $archivo);

        if (!File::exists($path)) {

            return response()->json([
                'success' => false,
                'message' => 'Archivo no encontrado'
            ], 404);

        }

        return Response::download($path);
    }
}