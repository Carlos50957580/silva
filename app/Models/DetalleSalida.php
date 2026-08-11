<?php
// app/Models/DetalleSalida.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    protected $table = 'detalle_salidas';

    protected $fillable = [
        'salida_id',
        'articulo_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function salida()
    {
        return $this->belongsTo(Salida::class);
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