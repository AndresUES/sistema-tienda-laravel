<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('cliente')->orderBy('fecha', 'desc')->paginate(10);
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        // Solo traemos productos que tengan stock positivo para evitar errores
        $productos = Producto::where('stock', '>', 0)->get();
        return view('ventas.create', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_documento' => 'required|in:FCF,CCF,TICKET',
            'numero_factura' => 'required|string|max:255',
            'productos_id' => 'required|array',
            'productos_id.*' => 'exists:productos,id',
            'cantidades' => 'required|array',
            'cantidades.*' => 'integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear el Encabezado
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'usuario_id' => Auth::id(), // ID del usuario logueado
                'numero_factura' => $request->numero_factura,
                'fecha' => now(), // Fecha y hora actual
                'tipo_documento' => $request->tipo_documento,
                'estado' => 'ACTIVA',
                'subtotal' => 0,
                'iva' => 0,
                'total' => 0,
            ]);

            $total_acumulado = 0;

            // 2. Procesar los Detalles
            foreach ($request->productos_id as $index => $producto_id) {
                $cantidad = $request->cantidades[$index];
                
                // BLOQUEO DE SEGURIDAD: Consultamos el producto y bloqueamos la fila
                // para evitar que dos personas vendan el mismo producto al mismo tiempo
                $producto = Producto::lockForUpdate()->find($producto_id);

                // Validación final de stock
                if ($producto->stock < $cantidad) {
                    throw new \Exception("Stock insuficiente para el producto: " . $producto->nombre . ". Disponible: " . $producto->stock);
                }

                $precio_venta = $producto->precio_venta; 
                $subtotal_linea = $cantidad * $precio_venta;
                $total_acumulado += $subtotal_linea;

                // Guardar Detalle
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'precio' => $precio_venta,
                    'subtotal' => $subtotal_linea,
                ]);

                // RESTAR STOCK (El paso más importante)
                $producto->stock -= $cantidad;
                $producto->save();
            }

            // 3. Calcular Impuestos (Asumiendo Precios con IVA Incluido)
            // Si el precio de venta es $113, entonces: Base $100 + IVA $13
            $subtotal = $total_acumulado / 1.13;
            $iva = $total_acumulado - $subtotal;

            $venta->update([
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total_acumulado
            ]);

            DB::commit();

            return redirect()->route('ventas.index')->with('success', 'Venta realizada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'usuario'])->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }
}