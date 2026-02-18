<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::orderBy('id', 'desc')->paginate(10);
        return view('marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre',
        ]);

        Marca::create($request->all());

        return redirect()->route('marcas.index')->with('success', 'Marca creada correctamente.');
    }

    public function edit($id)
    {
        $marca = Marca::findOrFail($id);
        return view('marcas.edit', compact('marca'));
    }

    public function update(Request $request, $id)
    {
        $marca = Marca::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre,' . $marca->id,
        ]);

        $marca->update($request->all());

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy($id)
    {
        $marca = Marca::findOrFail($id);
        
        // Validación de seguridad: No borrar si tiene productos
        if($marca->productos()->count() > 0) {
           return back()->with('error', 'No puedes eliminar esta marca porque tiene productos asociados.');
        }

        $marca->delete();
        return back()->with('success', 'Marca eliminada.');
    }
}