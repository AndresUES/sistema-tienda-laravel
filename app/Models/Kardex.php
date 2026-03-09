<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table = 'kardex';

    protected $fillable = [
        'producto_id',
        'tipo',
        'cantidad',
        'precio',
        'stock_anterior',
        'stock_nuevo',
        'fecha',
        'referencia_id'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
