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
        $ventas = Venta::with(['cliente', 'usuario'])->orderBy('fecha', 'desc')->paginate(10);
        return view('ventas.index', compact('ventas'));
    }

   public function create()
    {
        $clientes = Cliente::all();
        $productos = Producto::where('stock', '>', 0)->get();

        // LÓGICA DE CORRELATIVO AUTOMÁTICO
        // Buscamos la última venta registrada
        $ultimaVenta = Venta::latest('id')->first();
        
        // Si hay venta, sumamos 1. Si no, empezamos en 1.
        $siguienteId = $ultimaVenta ? $ultimaVenta->id + 1 : 1;
        
        // Rellenamos con ceros a la izquierda (Ej: 000001)
        $numeroFactura = str_pad($siguienteId, 8, '0', STR_PAD_LEFT);

        // Pasamos la variable a la vista
        return view('ventas.create', compact('clientes', 'productos', 'numeroFactura'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_documento' => 'required|in:FCF,CCF,TICKET',
            'numero_factura' => 'required|string|max:255',
            'productos_id' => 'required|array',
            'cantidades' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear Venta
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'usuario_id' => Auth::id(), // ID del usuario conectado
                'numero_factura' => $request->numero_factura,
                'fecha' => now(),
                'tipo_documento' => $request->tipo_documento,
                'estado' => 'ACTIVA',
                'subtotal' => 0,
                'iva' => 0,
                'total' => 0,
            ]);

            $total_acumulado = 0;

            // 2. Procesar Detalles
            foreach ($request->productos_id as $index => $producto_id) {
                $cantidad = $request->cantidades[$index];
                
                // Bloqueamos la fila para evitar ventas simultáneas del mismo stock
                $producto = Producto::lockForUpdate()->find($producto_id);

                // Validación estricta de Stock
                if ($producto->stock < $cantidad) {
                    throw new \Exception("Stock insuficiente para: " . $producto->nombre);
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

                // RESTAR STOCK
                $producto->stock -= $cantidad;
                $producto->save();
            }

            // 3. Totales
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
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'usuario'])->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }
}