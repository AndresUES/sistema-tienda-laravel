<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'dui',      
        'nit',      
        'nrc',      
        'direccion',
        'telefono',
        'email',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }
}