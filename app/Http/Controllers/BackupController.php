<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function generar()
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');

        $folder = storage_path('app/backups');

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $filename = 'backup_' . date('Y_m_d_H_i_s') . '.sql';

        $path = $folder . '/' . $filename;

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($database),
            escapeshellarg($path)
        );

        system($command, $resultado);

        if ($resultado !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo generar el respaldo'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Respaldo generado correctamente',
            'archivo' => $filename
        ]);
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