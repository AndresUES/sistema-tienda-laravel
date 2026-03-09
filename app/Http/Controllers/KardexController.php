<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    /**
     * Mostrar el historial de movimientos de inventario
     */
    public function index(Request $request)
    {
        $query = Kardex::with('producto')->orderBy('fecha', 'desc');

        // Filtro opcional por producto
        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        // Filtro opcional por tipo de movimiento
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $movimientos = $query->get();

        return view('kardex.index', compact('movimientos'));
    }
}