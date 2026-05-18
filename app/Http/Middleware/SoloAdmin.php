<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SoloAdmin
{
    public function handle(Request $request, Closure $next)
    {

        if (!$request->user()) {

            return response()->json([
                'message' => 'No autenticado'
            ], 401);

        }

        if ($request->user()->ID_rol != 1) {

            return response()->json([
                'message' => 'Acceso solo administrador'
            ], 403);

        }

        return $next($request);

    }
}