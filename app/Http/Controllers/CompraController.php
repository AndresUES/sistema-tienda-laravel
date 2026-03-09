<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with('proveedor')->orderBy('fecha', 'desc')->paginate(10);
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::all();
        // Traemos todos los productos (incluso con stock 0, porque vamos a comprar para reponer)
        $productos = Producto::all(); 
        return view('compras.create', compact('proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'numero_factura' => 'required|string|max:255',
            'fecha' => 'required|date',
            'productos_id' => 'required|array',
            'cantidades' => 'required|array',
            'precios' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear Compra (Sin user_id)
            $compra = Compra::create([
                'proveedor_id' => $request->proveedor_id,
                'numero_factura' => $request->numero_factura,
                'fecha' => $request->fecha,
                'subtotal' => 0,
                'iva' => 0,
                'total' => 0,
            ]);

            $total_acumulado = 0;

            // 2. Detalles
            foreach ($request->productos_id as $index => $producto_id) {
                $cantidad = $request->cantidades[$index];
                $precio = $request->precios[$index];
                $subtotal_linea = $cantidad * $precio;
                
                $total_acumulado += $subtotal_linea;

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'subtotal' => $subtotal_linea,
                ]);

                // 3. Actualizar Stock y Costo
                $producto = Producto::findOrFail($producto_id);
                $producto->stock += $cantidad;
                $producto->precio_compra = $precio;
                $producto->save();
            }

            // 4. Totales
            $subtotal = $total_acumulado / 1.13;
            $iva = $total_acumulado - $subtotal;

            $compra->update([
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total_acumulado
            ]);

            DB::commit();

            return redirect()->route('compras.index')->with('success', 'Compra registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'detalles.producto'])->findOrFail($id);
        return view('compras.show', compact('compra'));
    }
}