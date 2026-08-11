<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
    ];

    public function articulos()
    {
        return $this->hasMany(Articulo::class);
    }

    public function getTotalArticulosAttribute()
    {
        return $this->articulos()->count();
    }

    public function getStockTotalAttribute()
    {
        return $this->articulos()->sum('stock_actual');
    }

    // Scope para categorías activas
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}