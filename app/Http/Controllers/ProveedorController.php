<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::orderBy('id', 'desc')->paginate(10);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'required|string|max:20|unique:proveedores,nit',
            'nrc' => 'required|string|max:20',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
        ]);

        Proveedor::create($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            // Ignoramos el ID actual para que no de error de "ya existe" al editar
            'nit' => 'required|string|max:20|unique:proveedores,nit,' . $proveedor->id,
            'nrc' => 'required|string|max:20',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
        ]);

        $proveedor->update($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        
        if($proveedor->compras()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el proveedor porque tiene compras registradas.');
        }
        

        $proveedor->delete();
        return back()->with('success', 'Proveedor eliminado.');
    }
}