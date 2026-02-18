<?php

namespace App\Http\Controllers;

use App\Models\Categoria; // Tu modelo real
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriaController extends Controller
{
   public function index()
    {
        // Listar categorías (paginadas de 10 en 10)
        $categorias = Categoria::orderBy('id', 'desc')->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
            'descripcion' => 'nullable|string',
        ]);

        Categoria::create($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            // Validamos unique pero ignorando el ID actual para que no de error si no cambiamos el nombre
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        
        // Opcional: Evitar borrar si ya tiene productos (seguridad de datos)
        if($categoria->productos()->count() > 0) {
           return back()->with('error', 'No puedes eliminar esta categoría porque tiene productos asociados.');
        }

        $categoria->delete();
        return back()->with('success', 'Categoría eliminada.');
    }
}
