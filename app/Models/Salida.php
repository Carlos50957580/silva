<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salida extends Model
{
    use SoftDeletes;

    protected $table = 'salidas';

    protected $fillable = [
        'codigo',
        'sucursal_id',
        'usuario_id',
        'fecha_salida',
        'tipo',
        'estado',
        'observaciones',
        'destino',
        'total',
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'total' => 'decimal:2',
    ];

    /**
     * Relación con sucursal.
     *
     * withTrashed() permite mostrar la sucursal
     * aunque haya sido eliminada lógicamente.
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id')
            ->withTrashed();
    }

    /**
     * Relación con usuario.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación con detalles.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleSalida::class, 'salida_id');
    }

    /**
     * Scope para filtrar por sucursal.
     */
    public function scopeSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    /**
     * Scope para filtrar por estado.
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar por tipo.
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Badge del estado.
     */
    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'pendiente' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pendiente
                            </span>',

            'completada' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Completada
                            </span>',

            'cancelada' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Cancelada
                            </span>',
        ];

        return $badges[$this->estado] ?? $this->estado;
    }

    /**
     * Badge del tipo.
     */
    public function getTipoBadgeAttribute()
    {
        $badges = [
            'venta' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            Venta
                        </span>',

            'transferencia' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Transferencia
                                </span>',

            'consumo' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                              Consumo
                          </span>',

            'devolucion' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800">
                                Devolución
                            </span>',

            'baja' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                           Baja
                       </span>',
        ];

        return $badges[$this->tipo] ?? $this->tipo;
    }

    /**
     * Total formateado.
     */
    public function getTotalFormateadoAttribute()
    {
        return 'RD$ ' . number_format($this->total, 2);
    }

    /**
     * Nombre del tipo.
     */
    public function getTipoTextoAttribute()
    {
        $tipos = [
            'venta' => 'Venta',
            'transferencia' => 'Transferencia',
            'consumo' => 'Consumo Interno',
            'devolucion' => 'Devolución',
            'baja' => 'Baja de Inventario',
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }
}