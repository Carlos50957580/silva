<?php
// app/Models/Articulo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use SoftDeletes;

    protected $table = 'articulos';

    protected $fillable = [
        'codigo_sku',
        'nombre',
        'categoria_id',
        'stock_actual',
        'unidad_medida',
        'minimo_requerido',
        'ubicacion',
        'precio_unitario',
        'costo_unitario',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'costo_unitario' => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function alertas()
    {
        return $this->hasMany(AlertaStock::class);
    }

    public function getEstadoAttribute()
    {
        if ($this->stock_actual <= 0) {
            return 'agotado';
        } elseif ($this->stock_actual <= $this->minimo_requerido) {
            return 'stock_bajo';
        }
        return 'disponible';
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'disponible' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Disponible</span>',
            'stock_bajo' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Stock Bajo</span>',
            'agotado' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Agotado</span>'
        ];
        return $badges[$this->estado] ?? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Desconocido</span>';
    }

    public function tieneStockBajo()
    {
        return $this->stock_actual <= $this->minimo_requerido;
    }
}