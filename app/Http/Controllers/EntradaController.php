<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\DetalleEntrada;
use App\Models\Articulo;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    /**
     * Mostrar listado de entradas.
     */
    public function index(Request $request)
{
    $query = Entrada::with([
        'proveedor',
        'usuario',
        'sucursal'
    ]);

    // Búsqueda por código
    if ($request->filled('busqueda')) {
        $query->where(
            'codigo',
            'LIKE',
            "%{$request->busqueda}%"
        );
    }

    // Filtrar por estado
    if ($request->filled('estado')) {
        $query->where(
            'estado',
            $request->estado
        );
    }

    // Filtrar por sucursal
    if ($request->filled('sucursal_id')) {
        $query->where(
            'sucursal_id',
            $request->sucursal_id
        );
    }

    // Filtrar por fecha inicial
    if ($request->filled('fecha_inicio')) {
        $query->whereDate(
            'fecha_entrada',
            '>=',
            $request->fecha_inicio
        );
    }

    // Filtrar por fecha final
    if ($request->filled('fecha_fin')) {
        $query->whereDate(
            'fecha_entrada',
            '<=',
            $request->fecha_fin
        );
    }

    $entradas = $query
        ->orderBy('created_at', 'desc')
        ->paginate(15)
        ->withQueryString();

    // ==========================================
    // DEBUG - COMPROBAR SUCURSAL
    // ==========================================


    // Sucursales disponibles para el filtro
    $sucursales = Sucursal::where('activo', true)
        ->orderBy('nombre')
        ->get();

    $stats = [
        'total' => Entrada::count(),

        'pendientes' => Entrada::where(
            'estado',
            'pendiente'
        )->count(),

        'completadas' => Entrada::where(
            'estado',
            'completada'
        )->count(),

        'canceladas' => Entrada::where(
            'estado',
            'cancelada'
        )->count(),
    ];

    return view(
        'entradas.index',
        compact(
            'entradas',
            'stats',
            'sucursales'
        )
    );
}

    /**
     * Mostrar formulario para crear una entrada.
     */
    public function create()
    {
        $proveedores = Proveedor::where(
            'activo',
            true
        )
            ->orderBy('nombre')
            ->get();

        $articulos = Articulo::where(
            'activo',
            true
        )
            ->orderBy('nombre')
            ->get();

        // Sucursales disponibles
        $sucursales = Sucursal::where(
            'activo',
            true
        )
            ->orderBy('nombre')
            ->get();

        // Generar código automático
        $ultimo = Entrada::latest('id')->first();

        $numero = $ultimo
            ? intval(substr($ultimo->codigo, -4)) + 1
            : 1;

        $codigo = 'ENT-' .
            date('Ymd') .
            '-' .
            str_pad(
                $numero,
                4,
                '0',
                STR_PAD_LEFT
            );

        return view(
            'entradas.create',
            compact(
                'proveedores',
                'articulos',
                'sucursales',
                'codigo'
            )
        );
    }

    /**
     * Registrar una nueva entrada.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|unique:entradas,codigo',

            'sucursal_id' => [
                'required',
                'exists:sucursales,id'
            ],

            'proveedor_id' => [
                'nullable',
                'exists:proveedores,id'
            ],

            'fecha_entrada' => [
                'required',
                'date'
            ],

            'observaciones' => [
                'nullable',
                'string'
            ],

            'articulos' => [
                'required',
                'array',
                'min:1'
            ],

            'articulos.*.id' => [
                'required',
                'exists:articulos,id'
            ],

            'articulos.*.cantidad' => [
                'required',
                'integer',
                'min:1'
            ],

            'articulos.*.precio' => [
                'required',
                'numeric',
                'min:0'
            ],
        ]);

        try {
            DB::beginTransaction();

            // Calcular total
            $total = 0;

            foreach ($validated['articulos'] as $item) {
                $total +=
                    $item['cantidad'] *
                    $item['precio'];
            }

            /*
             * Crear entrada
             */
            $entrada = Entrada::create([
                'codigo' => $validated['codigo'],

                'sucursal_id' => $validated['sucursal_id'],

                'proveedor_id' => $validated['proveedor_id'] ?? null,

                'usuario_id' => auth()->id(),

                'fecha_entrada' => $validated['fecha_entrada'],

                'observaciones' => $validated['observaciones'] ?? null,

                'total' => $total,

                'estado' => 'completada'
            ]);

            /*
             * Crear detalles y actualizar inventario
             */
            foreach ($validated['articulos'] as $item) {

                $articulo = Articulo::findOrFail(
                    $item['id']
                );

                $subtotal =
                    $item['cantidad'] *
                    $item['precio'];

                /*
                 * Detalle de entrada
                 */
                DetalleEntrada::create([
                    'entrada_id' => $entrada->id,

                    'articulo_id' => $item['id'],

                    'cantidad' => $item['cantidad'],

                    'precio_unitario' => $item['precio'],

                    'subtotal' => $subtotal
                ]);

                /*
                 * Actualizar stock
                 */
                $articulo->increment(
                    'stock_actual',
                    $item['cantidad']
                );

                /*
                 * Registrar movimiento de inventario
                 *
                 * IMPORTANTE:
                 * Esto requiere que movimientos_inventario
                 * también tenga sucursal_id.
                 */
                MovimientoInventario::create([
                    'articulo_id' => $item['id'],

                    'sucursal_id' => $validated['sucursal_id'],

                    'tipo' => 'entrada',

                    'cantidad' => $item['cantidad'],

                    'precio_unitario' => $item['precio'],

                    'motivo' =>
                        'Entrada de inventario - ' .
                        $entrada->codigo,

                    'usuario_id' => auth()->id(),
                ]);

                /*
                 * Verificar alerta de stock
                 */
                $this->verificarAlertaStock(
                    $articulo
                );
            }

            DB::commit();

            return redirect()
                ->route('entradas.index')
                ->with(
                    'success',
                    'Entrada registrada exitosamente.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Error al registrar la entrada: ' .
                    $e->getMessage()
                );
        }
    }

    /**
     * Mostrar una entrada.
     */
    public function show($id)
    {
        $entrada = Entrada::with([
            'proveedor',
            'usuario',
            'sucursal',
            'detalles.articulo'
        ])->findOrFail($id);

        return view(
            'entradas.show',
            compact('entrada')
        );
    }

    /**
     * Mostrar formulario para editar una entrada.
     */
    public function edit($id)
    {
        $entrada = Entrada::with([
            'detalles.articulo',
            'sucursal'
        ])->findOrFail($id);

        if ($entrada->estado !== 'pendiente') {

            return redirect()
                ->route('entradas.index')
                ->with(
                    'error',
                    'No se puede editar una entrada que ya está ' .
                    $entrada->estado
                );
        }

        $proveedores = Proveedor::where(
            'activo',
            true
        )
            ->orderBy('nombre')
            ->get();

        $articulos = Articulo::where(
            'activo',
            true
        )
            ->orderBy('nombre')
            ->get();

        // Sucursales disponibles
        $sucursales = Sucursal::where(
            'activo',
            true
        )
            ->orderBy('nombre')
            ->get();

        return view(
            'entradas.edit',
            compact(
                'entrada',
                'proveedores',
                'articulos',
                'sucursales'
            )
        );
    }

    /**
     * Actualizar una entrada.
     */
    public function update(Request $request, $id)
    {
        $entrada = Entrada::with('detalles')
            ->findOrFail($id);

        if ($entrada->estado !== 'pendiente') {

            return redirect()
                ->route('entradas.index')
                ->with(
                    'error',
                    'No se puede editar una entrada que ya está ' .
                    $entrada->estado
                );
        }

        $validated = $request->validate([
            'sucursal_id' => [
                'required',
                'exists:sucursales,id'
            ],

            'proveedor_id' => [
                'nullable',
                'exists:proveedores,id'
            ],

            'fecha_entrada' => [
                'required',
                'date'
            ],

            'observaciones' => [
                'nullable',
                'string'
            ],

            'articulos' => [
                'required',
                'array',
                'min:1'
            ],

            'articulos.*.id' => [
                'required',
                'exists:articulos,id'
            ],

            'articulos.*.cantidad' => [
                'required',
                'integer',
                'min:1'
            ],

            'articulos.*.precio' => [
                'required',
                'numeric',
                'min:0'
            ],
        ]);

        try {
            DB::beginTransaction();

            /*
             * Guardamos la sucursal anterior.
             * Esto nos permite actualizar correctamente
             * los movimientos relacionados.
             */
            $sucursalAnteriorId =
                $entrada->sucursal_id;

            /*
             * Revertir stock de los detalles anteriores
             */
            foreach ($entrada->detalles as $detalle) {

                $articulo = Articulo::find(
                    $detalle->articulo_id
                );

                if ($articulo) {

                    $articulo->decrement(
                        'stock_actual',
                        $detalle->cantidad
                    );
                }

                /*
                 * Eliminar movimiento relacionado
                 */
                MovimientoInventario::where(
                    'articulo_id',
                    $detalle->articulo_id
                )
                    ->where(
                        'tipo',
                        'entrada'
                    )
                    ->where(
                        'motivo',
                        'LIKE',
                        '%' . $entrada->codigo . '%'
                    )
                    ->delete();
            }

            /*
             * Eliminar detalles anteriores
             */
            $entrada->detalles()->delete();

            /*
             * Calcular nuevo total
             */
            $total = 0;

            foreach ($validated['articulos'] as $item) {

                $total +=
                    $item['cantidad'] *
                    $item['precio'];
            }

            /*
             * Actualizar entrada
             */
            $entrada->update([
                'sucursal_id' =>
                    $validated['sucursal_id'],

                'proveedor_id' =>
                    $validated['proveedor_id'] ?? null,

                'fecha_entrada' =>
                    $validated['fecha_entrada'],

                'observaciones' =>
                    $validated['observaciones'] ?? null,

                'total' => $total,
            ]);

            /*
             * Crear nuevos detalles
             * y actualizar stock
             */
            foreach ($validated['articulos'] as $item) {

                $articulo = Articulo::findOrFail(
                    $item['id']
                );

                $subtotal =
                    $item['cantidad'] *
                    $item['precio'];

                /*
                 * Crear detalle
                 */
                DetalleEntrada::create([
                    'entrada_id' => $entrada->id,

                    'articulo_id' => $item['id'],

                    'cantidad' => $item['cantidad'],

                    'precio_unitario' =>
                        $item['precio'],

                    'subtotal' => $subtotal
                ]);

                /*
                 * Actualizar stock
                 */
                $articulo->increment(
                    'stock_actual',
                    $item['cantidad']
                );

                /*
                 * Crear movimiento
                 */
                MovimientoInventario::create([
                    'articulo_id' => $item['id'],

                    'sucursal_id' =>
                        $validated['sucursal_id'],

                    'tipo' => 'entrada',

                    'cantidad' =>
                        $item['cantidad'],

                    'precio_unitario' =>
                        $item['precio'],

                    'motivo' =>
                        'Entrada de inventario - ' .
                        $entrada->codigo,

                    'usuario_id' =>
                        auth()->id(),
                ]);

                /*
                 * Verificar alerta
                 */
                $this->verificarAlertaStock(
                    $articulo
                );
            }

            DB::commit();

            return redirect()
                ->route(
                    'entradas.show',
                    $entrada->id
                )
                ->with(
                    'success',
                    'Entrada actualizada exitosamente.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Error al actualizar la entrada: ' .
                    $e->getMessage()
                );
        }
    }

    /**
     * Eliminar una entrada.
     */
    public function destroy($id)
    {
        $entrada = Entrada::with('detalles')
            ->findOrFail($id);

        if ($entrada->estado === 'completada') {

            return back()
                ->with(
                    'error',
                    'No se puede eliminar una entrada completada.'
                );
        }

        try {
            DB::beginTransaction();

            /*
             * Si en el futuro se permite eliminar
             * entradas completadas, aquí se revertiría
             * el stock.
             */
            if ($entrada->estado === 'completada') {

                foreach ($entrada->detalles as $detalle) {

                    $articulo = Articulo::find(
                        $detalle->articulo_id
                    );

                    if ($articulo) {

                        $articulo->decrement(
                            'stock_actual',
                            $detalle->cantidad
                        );
                    }
                }
            }

            $entrada->delete();

            DB::commit();

            return redirect()
                ->route('entradas.index')
                ->with(
                    'success',
                    'Entrada eliminada exitosamente.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with(
                    'error',
                    'Error al eliminar la entrada: ' .
                    $e->getMessage()
                );
        }
    }

    /**
     * Cancelar una entrada.
     */
    public function cancelar($id)
    {
        $entrada = Entrada::findOrFail($id);

        if ($entrada->estado !== 'pendiente') {

            return back()
                ->with(
                    'error',
                    'No se puede cancelar una entrada que ya está ' .
                    $entrada->estado
                );
        }

        try {
            DB::beginTransaction();

            $entrada->update([
                'estado' => 'cancelada'
            ]);

            DB::commit();

            return redirect()
                ->route('entradas.index')
                ->with(
                    'success',
                    'Entrada cancelada exitosamente.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with(
                    'error',
                    'Error al cancelar la entrada: ' .
                    $e->getMessage()
                );
        }
    }

    /**
     * Verificar alerta de stock.
     */
    private function verificarAlertaStock($articulo)
    {
        if (
            $articulo->stock_actual <=
            $articulo->minimo_requerido
        ) {

            \App\Models\AlertaStock::updateOrCreate(
                [
                    'articulo_id' =>
                        $articulo->id,

                    'estado' =>
                        'pendiente'
                ],
                [
                    'stock_actual' =>
                        $articulo->stock_actual,

                    'minimo_requerido' =>
                        $articulo->minimo_requerido,

                    'comentarios' =>
                        'Stock por debajo del mínimo requerido.'
                ]
            );

        } else {

            \App\Models\AlertaStock::where(
                'articulo_id',
                $articulo->id
            )
                ->where(
                    'estado',
                    'pendiente'
                )
                ->update([
                    'estado' => 'resuelta',

                    'comentarios' =>
                        'Stock restablecido a niveles normales.'
                ]);
        }
    }
}