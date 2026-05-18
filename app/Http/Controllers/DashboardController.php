<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function usuariosPorMes()
    {
        $data = DB::table('usuario')
            ->selectRaw('
                DATE_FORMAT(fecha_registro, "%Y-%m") as mes,
                COUNT(*) as total
            ')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function ventasPorMes()
    {
        $data = DB::table('pedido')
            ->selectRaw('
                DATE_FORMAT(fec_ped, "%Y-%m") as mes,
                COUNT(*) as compras,
                SUM(pago_total) as total_ventas
            ')
            ->where('estado', '!=', 'carrito')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function prediccionCompras()
    {
        $ventas = DB::table('pedido')
            ->selectRaw('
                DATE_FORMAT(fec_ped, "%Y-%m") as mes,
                COUNT(*) as compras
            ')
            ->where('estado', '!=', 'carrito')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $n = count($ventas);

        if ($n < 2) {
            return response()->json([
                'prediccion' => 0,
                'mensaje' => 'No hay suficientes datos para predecir'
            ]);
        }

        $x = [];
        $y = [];

        foreach ($ventas as $index => $venta) {
            $x[] = $index + 1;
            $y[] = $venta->compras;
        }

        $sumX = array_sum($x);
        $sumY = array_sum($y);

        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
        }

        $pendiente = (($n * $sumXY) - ($sumX * $sumY)) /
            (($n * $sumX2) - ($sumX * $sumX));

        $intercepto = ($sumY - ($pendiente * $sumX)) / $n;

        $siguienteMes = $n + 1;

        $prediccion = round(
            $intercepto + ($pendiente * $siguienteMes)
        );

        if ($prediccion < 0) {
            $prediccion = 0;
        }

        return response()->json([
            'prediccion' => $prediccion,
            'modelo' => 'Regresión lineal simple',
            'data' => $ventas
        ]);
    }
}