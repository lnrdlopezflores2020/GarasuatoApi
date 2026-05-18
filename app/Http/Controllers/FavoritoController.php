<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritoController extends Controller
{

    public function agregar($id)
    {

        auth()->user()->favoritos()->syncWithoutDetaching([$id]);

        return response()->json([
            'success' => true
        ]);

    }


    public function quitar($id)
    {

        auth()->user()->favoritos()->detach($id);

        return response()->json([
            'success' => true
        ]);

    }


    public function index()
    {

        $favoritos = auth()->user()
            ->favoritos()
            ->with('categorias')
            ->get();

        return view('cpanel.favoritos.index', compact('favoritos'));

    }

    public function apiAgregar($id)
    {
        auth()->user()
            ->favoritos()
            ->syncWithoutDetaching([$id]);

        return response()->json([
            'success' => true
        ]);
    }

    public function apiQuitar($id)
    {
        auth()->user()
            ->favoritos()
            ->detach($id);

        return response()->json([
            'success' => true
        ]);
    }

    public function apiFavoritos()
    {
        $favoritos = auth()->user()
            ->favoritos()
            ->with('categorias')
            ->get();

        return response()->json($favoritos);
    }
}