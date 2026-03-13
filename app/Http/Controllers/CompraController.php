<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kardex;

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

                // Guardar Detalle de Compra
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'subtotal' => $subtotal_linea,
                ]);

                // 1. OBTENER PRODUCTO Y CAPTURAR STOCK ANTERIOR
                $producto = Producto::findOrFail($producto_id);
                $stockAnterior = $producto->stock; // Guardamos el estado actual

                // 2. ACTUALIZAR STOCK Y COSTO
                $producto->stock += $cantidad; // Sumamos la entrada
                $producto->precio_compra = $precio;
                $producto->save();

                // 3. REGISTRAR EN KARDEX
                Kardex::create([
                    'producto_id'    => $producto_id,
                    'tipo'           => 'COMPRA',       // Identifica la entrada
                    'cantidad'       => $cantidad,
                    'precio'         => $precio,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo'    => $producto->stock, // El stock ya actualizado
                    'fecha'          => $request->fecha,  // Usamos la fecha de la factura
                    'referencia_id'  => $compra->id,      // ID de la compra para auditoría
                ]);
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