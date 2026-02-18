<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos'; // Tu tabla real

    protected $fillable = [
        'codigo_barra',
        'nombre',
        'descripcion',
        'categoria_id',
        'marca_id',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'iva',
    ];

    // Relación: Un producto pertenece a una Categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    // Relación: Un producto pertenece a una Marca
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }
}