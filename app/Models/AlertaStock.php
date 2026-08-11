<?php
// app/Models/AlertaStock.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaStock extends Model
{
    protected $table = 'alertas_stock';
    
    protected $fillable = [
        'articulo_id',
        'stock_actual',
        'minimo_requerido',
        'estado',
        'comentarios'
    ];

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'pendiente' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Pendiente</span>',
            'resuelta' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Resuelta</span>'
        ];
        return $badges[$this->estado] ?? $this->estado;
    }
}