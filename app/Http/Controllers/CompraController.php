<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompraController extends Controller
{
    public function index()
    {
        // Listamos las compras ordenadas por fecha descendente
        $compras = Compra::with('proveedor')->orderBy('fecha', 'desc')->paginate(10);
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::all();
        // Solo mostramos productos habilitados para la compra
        $productos = Producto::where('stock', '>=', 0)->get(); 
        return view('compras.create', compact('proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        // 1. Validaciones
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'numero_factura' => 'required|string|max:255',
            'fecha' => 'required|date',
            // Arrays que vienen de la vista (JavaScript)
            'productos_id' => 'required|array',
            'productos_id.*' => 'exists:productos,id',
            'cantidades' => 'required|array',
            'cantidades.*' => 'integer|min:1',
            'precios' => 'required|array',
            'precios.*' => 'numeric|min:0',
        ]);

        try {
            DB::beginTransaction(); // Iniciamos la transacción de base de datos

            // 2. Crear el Encabezado de la Compra
            // Inicializamos en 0, luego actualizamos con la suma real
            $compra = Compra::create([
                'proveedor_id' => $request->proveedor_id,
                'numero_factura' => $request->numero_factura,
                'fecha' => $request->fecha,
                'user_id' => Auth::id(), // Guardamos quién registró la compra
                'subtotal' => 0,
                'iva' => 0,
                'total' => 0,
            ]);

            $total_acumulado = 0;

            // 3. Recorrer los productos y guardar el detalle
            // Usamos el índice del array para sincronizar producto, cantidad y precio
            foreach ($request->productos_id as $index => $producto_id) {
                $cantidad = $request->cantidades[$index];
                $costo = $request->precios[$index];
                $subtotal_linea = $cantidad * $costo;
                
                $total_acumulado += $subtotal_linea;

                // A. Crear registro en detalle_compras
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'precio' => $costo,
                    'subtotal' => $subtotal_linea,
                ]);

                // B. Actualizar Stock y Costo en la tabla PRODUCTOS
                $producto = Producto::findOrFail($producto_id);
                $producto->stock += $cantidad; // Sumamos al inventario actual
                $producto->precio_compra = $costo; // Actualizamos el costo al último precio de compra
                $producto->save();
            }

            // 4. Actualizar los totales de la Compra
            // Asumiendo que los precios ingresados YA incluyen IVA (común en facturas finales)
            // Si tus precios son MAS IVA, la fórmula cambia. Aquí asumo IVA INCLUIDO (13% El Salvador)
            
            $subtotal = $total_acumulado / 1.13;
            $iva = $total_acumulado - $subtotal;

            $compra->update([
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total_acumulado
            ]);

            DB::commit(); // Confirmamos los cambios en la BD

            return redirect()->route('compras.index')->with('success', 'Compra registrada exitosamente y stock actualizado.');

        } catch (\Exception $e) {
            DB::rollBack(); // Si algo falla, deshacemos todo para no dejar datos corruptos
            return back()->with('error', 'Error al procesar la compra: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        // Cargamos la compra con sus relaciones para ver el detalle
        $compra = Compra::with(['proveedor', 'detalles.producto', 'usuario'])->findOrFail($id);
        return view('compras.show', compact('compra'));
    }
}