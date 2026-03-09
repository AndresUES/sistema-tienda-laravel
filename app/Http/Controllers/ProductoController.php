<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Kardex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        // Usamos 'with' para traer los nombres de categoría y marca optimizados
        $productos = Producto::with(['categoria', 'marca'])->orderBy('id', 'desc')->paginate(10);
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        // Enviamos las listas para los <select>
        $categorias = Categoria::all();
        $marcas = Marca::all();
        return view('productos.create', compact('categorias', 'marcas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_barra' => 'required|unique:productos,codigo_barra',
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'required|exists:marcas,id',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'integer|min:0',
        ]);

        DB::transaction(function () use ($request) {

            $producto = Producto::create($request->all());

            // si el stock inicial es mayor a 0 se registra en kardex
            if ($producto->stock > 0) {

                Kardex::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'AJUSTE',
                    'cantidad' => $producto->stock,
                    'precio' => $producto->precio_compra,
                    'stock_anterior' => 0,
                    'stock_nuevo' => $producto->stock,
                    'fecha' => now(),
                    'referencia_id' => null
                ]);
            }
        });

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado y movimiento inicial registrado.');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $marcas = Marca::all();
        return view('productos.edit', compact('producto', 'categorias', 'marcas'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            // Ignoramos el ID actual para la validación de unique
            'codigo_barra' => 'required|unique:productos,codigo_barra,' . $producto->id,
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required',
            'marca_id' => 'required',
            'precio_venta' => 'required|numeric',
        ]);

        $producto->update($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return back()->with('success', 'Producto eliminado.');
    }
}