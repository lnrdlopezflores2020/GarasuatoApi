<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Models\Comentario;
//use App\Models\ModeloTelefono;

class ProductoController extends Controller
{
    // LISTAR PRODUCTOS
    public function index()
    {
        $productos = Producto::with('categorias')->get();
       return view('cpanel.productos.index', compact('productos'));

    }

    // FORMULARIO CREAR
    public function create()
    {
        $categorias = Categoria::all();
        return view('cpanel.productos.create', compact('categorias'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'Precio_uni' => 'required|numeric',
            'imagen' => 'required|image',
            'categorias' => 'required|array'
        ]);

        
        if (!$request->hasFile('imagen')) {
            return back()->with('error', 'No se recibió la imagen');
        }
        
        
        // Guardar imagen
        $imagen = $request->file('imagen');

        // Generar nombre único
        $nombreImagen = time() . '_' . $imagen->getClientOriginalName();

        // Mover manualmente
        $imagen->move(public_path('storage/productos'), $nombreImagen);

        // Guardar ruta
        $ruta = 'productos/' . $nombreImagen;

        $producto = Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'Precio_uni' => $request->Precio_uni,
            'imagen' => $ruta
        ]);

        // Relación muchos a muchos
        $producto->categorias()->sync($request->categorias);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto agregado correctamente');
    }

    // ELIMINAR
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->categorias()->detach();
        $producto->delete();

        return back()->with('success', 'Producto eliminado');
    }

    public function edit($id)
    {
        $producto = Producto::with('categorias')->findOrFail($id);
        $categorias = Categoria::all();

        return view('cpanel.productos.edit', compact('producto', 'categorias'));
    }
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'Precio_uni' => 'required|numeric',
            'categorias' => 'required|array'
        ]);

        // Si se sube nueva imagen
        if ($request->hasFile('imagen')) {
            $ruta = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $ruta;
        }

        $producto->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'Precio_uni' => $request->Precio_uni,
        ]);

        // Actualizar relación muchos a muchos
        $producto->categorias()->sync($request->categorias);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function show($id)
    {
        $producto = Producto::with([
            'favoritos',
            'comentarios.usuario'
        ])->findOrFail($id);

        $promedio = Comentario::where('ID_prod', $id)
            ->avg('calificacion');

        $totalComentarios = Comentario::where('ID_prod', $id)
            ->count();

        return view('cpanel.productos.show', compact(
            'producto',
            'promedio',
            'totalComentarios'
        ));
    }

    public function apiIndex()
    {
        $categorias = Categoria::with('productos')
            ->get();

        $favoritosIds = [];

        if (auth('sanctum')->check()) {

            $favoritosIds = auth('sanctum')
                ->user()
                ->favoritos()
                ->pluck('producto.ID_prod')
                ->toArray();

        }

        foreach ($categorias as $categoria) {

            foreach ($categoria->productos as $producto) {

                $producto->imagen =
                    asset('storage/' . $producto->imagen);

                $producto->es_favorito =
                    in_array(
                        $producto->ID_prod,
                        $favoritosIds
                    );

            }

        }

        return response()->json([

            'categorias' => $categorias

        ]);
    }

    public function apiShow($id)
    {
        $producto = Producto::with([

            'categorias',
            'favoritos',
            'comentarios.usuario'

        ])->findOrFail($id);

        $promedio = Comentario::where(
            'ID_prod',
            $id
        )->avg('calificacion');

        $totalComentarios = Comentario::where(
            'ID_prod',
            $id
        )->count();

        return response()->json([

            'producto' => $producto,

            'promedio' => round($promedio, 1),

            'totalComentarios' => $totalComentarios

        ]);
    }

        public function apiAdminIndex()
    {
        $productos = Producto::with('categorias')->get();

        foreach ($productos as $producto) {

            $producto->imagen_url =
                asset('storage/' . $producto->imagen);

        }

        return response()->json([
            'productos' => $productos
        ]);
    }

    public function apiCategorias()
    {
        $categorias = Categoria::all();

        return response()->json([
            'categorias' => $categorias
        ]);
    }

    public function apiAdminShow($id)
    {
        $producto = Producto::with('categorias')
            ->findOrFail($id);

        $producto->imagen_url =
            asset('storage/' . $producto->imagen);

        return response()->json([
            'producto' => $producto
        ]);
    }

    public function apiAdminStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'Precio_uni' => 'required|numeric',
            'imagen' => 'required|image',
            'categorias' => 'required|array'
        ]);

        $imagen = $request->file('imagen');

        $nombreImagen =
            time() . '_' . $imagen->getClientOriginalName();

        $imagen->move(
            public_path('storage/productos'),
            $nombreImagen
        );

        $ruta =
            'productos/' . $nombreImagen;

        $producto = Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'Precio_uni' => $request->Precio_uni,
            'imagen' => $ruta
        ]);

        $producto->categorias()->sync(
            $request->categorias
        );

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado correctamente'
        ]);
    }

    public function apiAdminUpdate(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'Precio_uni' => 'required|numeric',
            'categorias' => 'required|array'
        ]);

        if ($request->hasFile('imagen')) {

            $ruta = $request->file('imagen')
                ->store('productos', 'public');

            $producto->imagen = $ruta;

        }

        $producto->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'Precio_uni' => $request->Precio_uni,
            'imagen' => $producto->imagen
        ]);

        $producto->categorias()->sync(
            $request->categorias
        );

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente'
        ]);
    }

    public function apiAdminDestroy($id)
    {
        $producto = Producto::findOrFail($id);

        $producto->categorias()->detach();

        $producto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);
    }
}