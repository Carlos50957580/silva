<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'articulo_id',
        'sucursal_id',
        'tipo',
        'cantidad',
        'precio_unitario',
        'motivo',
        'usuario_id',
    ];

    // Relaciones

    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Accesores

    public function getTipoTextoAttribute()
    {
        $tipos = [
            'entrada' => 'Entrada',
            'salida' => 'Salida',
            'ajuste' => 'Ajuste',
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }

    public function getTipoColorAttribute()
    {
        $colores = [
            'entrada' => 'success',
            'salida' => 'danger',
            'ajuste' => 'warning',
        ];

        return $colores[$this->tipo] ?? 'secondary';
    }

    public function getTipoIconoAttribute()
    {
        $iconos = [
            'entrada' => 'fa-arrow-down',
            'salida' => 'fa-arrow-up',
            'ajuste' => 'fa-edit',
        ];

        return $iconos[$this->tipo] ?? 'fa-circle';
    }

    public function getTipoBadgeAttribute()
    {
        $badges = [
            'entrada' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-arrow-down mr-1"></i> Entrada
                          </span>',

            'salida' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            <i class="fas fa-arrow-up mr-1"></i> Salida
                          </span>',

            'ajuste' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            <i class="fas fa-edit mr-1"></i> Ajuste
                          </span>',
        ];

        return $badges[$this->tipo] ?? $this->tipo;
    }

    // Scope para filtrar por fecha

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('created_at', [$inicio, $fin]);
    }

    // Scope para filtrar por tipo

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Scope para filtrar por sucursal

    public function scopeSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }
}