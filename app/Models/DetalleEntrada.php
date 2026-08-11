<?php
// app/Models/DetalleEntrada.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleEntrada extends Model
{
    protected $table = 'detalle_entradas';

    protected $fillable = [
        'entrada_id',
        'articulo_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function entrada()
    {
        return $this->belongsTo(Entrada::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function getSubtotalFormateadoAttribute()
    {
        return 'RD$ ' . number_format($this->subtotal, 2);
    }

    public function getPrecioUnitarioFormateadoAttribute()
    {
        return 'RD$ ' . number_format($this->precio_unitario, 2);
    }
}