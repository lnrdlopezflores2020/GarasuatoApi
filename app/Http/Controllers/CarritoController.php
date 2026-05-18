<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Contiene;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarritoController extends Controller
{

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $pedido = Pedido::where('ID_usu', auth()->user()->ID_usu)
            ->where('estado', 'carrito')
            ->with('productos')
            ->first();

        return view('carrito.index', compact('pedido'));
    }

    public function agregar(Request $request)
    {
        try {

            if (!Auth::check()) {
                return response()->json(['redirect' => route('login')]);
            }

            $usuario = Auth::user();

            // 🔹 Buscar o crear carrito
            $pedido = Pedido::firstOrCreate(
                [
                    'ID_usu' => $usuario->ID_usu,
                    'estado' => 'carrito'
                ],
                [
                    'pago_total' => 0
                ]
            );

            //Obtener producto
            $producto = Producto::find($request->ID_prod);

            $cantidad = max(1, (int)$request->cantidad);

            if (!$producto) {
                return response()->json(['error' => 'Producto no encontrado'], 400);
            }

            $personalizacion = $request->personalizacion;

            //Ver si ya existe en carrito
            $existe = Contiene::where('num_ped', $pedido->num_ped)
                ->where('ID_prod', $producto->ID_prod)
                ->where('personalizacion', $personalizacion)
                ->first();

            if  ($existe) {
                Contiene::where('num_ped', $pedido->num_ped)
                    ->where('ID_prod', $producto->ID_prod)
                    ->update([
                        'cantidad' => $existe->cantidad + $cantidad
                    ]);
            
            } else {
                Contiene::create([
                    'num_ped' => $pedido->num_ped,
                    'ID_prod' => $producto->ID_prod,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->Precio_uni,
                    'personalizacion' => $personalizacion
                ]);
            }

            //  Actualizar total
            $total = Contiene::where('num_ped', $pedido->num_ped)
                ->sum(DB::raw('cantidad * precio_unitario'));

            $pedido->pago_total = $total;
            $pedido->save();

            // CONTADOR DEL CARRITO
            $count = Contiene::where('num_ped', $pedido->num_ped)
                ->sum('cantidad');

            session(['carrito_count' => $count]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            // mejor que dd
            return response()->json([
                'error' => 'Error en servidor',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
    public function eliminar(Request $request)
    {
        try {

            $usuario = Auth::user();

            // Buscar carrito activo
            $pedido = Pedido::where('ID_usu', $usuario->ID_usu)
                ->where('estado', 'carrito')
                ->first();

            if (!$pedido) {
                return back()->with('error', 'Carrito no encontrado');
            }

            // Eliminar producto específico
            Contiene::where('num_ped', $pedido->num_ped)
                ->where('ID_prod', $request->ID_prod)
                ->delete();

            // Recalcular total
            $total = Contiene::where('num_ped', $pedido->num_ped)
                ->sum(\DB::raw('cantidad * precio_unitario'));

            $pedido->pago_total = $total;
            $pedido->save();

            $count = Contiene::where('num_ped', $pedido->num_ped)
                ->sum('cantidad');

            session(['carrito_count' => $count]);

            return back()->with('success', 'Producto eliminado del carrito');

        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function actualizar(Request $request)
    {
        $usuario = Auth::user();

        $pedido = Pedido::where('ID_usu', $usuario->ID_usu)
            ->where('estado', 'carrito')
            ->first();

        if (!$pedido) {
            return response()->json(['error' => 'Pedido no encontrado'], 404);
        }

        $data = $request->json()->all();

        $ID_prod = $data['ID_prod'] ?? null;
        $cantidad = max(1, (int)($data['cantidad'] ?? 1));

        // UPDATE DIRECTO
        Contiene::where('num_ped', $pedido->num_ped)
            ->where('ID_prod', $ID_prod)
            ->update([
                'cantidad' => $cantidad
            ]);

        // recalcular total
        $total = Contiene::where('num_ped', $pedido->num_ped)
            ->sum(DB::raw('cantidad * precio_unitario'));

        $pedido->pago_total = $total;
        $pedido->save();

        // contador
        $count = Contiene::where('num_ped', $pedido->num_ped)
            ->sum('cantidad');

        session(['carrito_count' => $count]);

        return response()->json([
            'success' => true,
            'total' => $total,
            'count' => $count
        ]);
    }

        // API - VER CARRITO
    public function apiIndex()
    {
        $pedido = Pedido::where(
            'ID_usu',
            auth()->user()->ID_usu
        )
        ->where(
            'estado',
            'carrito'
        )
        ->with('productos')
        ->first();

        if (!$pedido) {

            return response()->json([

                'productos' => [],

                'total' => 0

            ]);

        }

        // IMAGEN COMPLETA
        foreach ($pedido->productos as $producto) {

            $producto->imagen =
                asset('storage/' . $producto->imagen);

        }

        foreach ($pedido->productos as $producto) {

            $producto->pivot->personalizacion = json_decode(
                $producto->pivot->personalizacion,
                true
            );

        }

        return response()->json([

            'productos' => $pedido->productos,

            'total' => $pedido->pago_total

        ]);
    }

    public function apiAgregar(Request $request)
    {
        return $this->agregar($request);
    }

    public function apiActualizar(Request $request)
    {
        return $this->actualizar($request);
    }

    public function apiEliminar($id)
    {
        $usuario = Auth::user();

        $pedido = Pedido::where(
            'ID_usu',
            $usuario->ID_usu
        )
        ->where(
            'estado',
            'carrito'
        )
        ->first();

        if (!$pedido) {

            return response()->json([
                'success' => false
            ], 404);

        }

        Contiene::where(
            'num_ped',
            $pedido->num_ped
        )
        ->where(
            'ID_contiene',
            $id
        )
        ->delete();

        $total = Contiene::where(
            'num_ped',
            $pedido->num_ped
        )
        ->sum(DB::raw(
            'cantidad * precio_unitario'
        ));

        $pedido->pago_total = $total;

        $pedido->save();

        return response()->json([
            'success' => true,
            'total' => $total
        ]);
    }
}
