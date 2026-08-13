<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Reporte de Inventario</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 25px;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
        }

        .subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .date {
            text-align: right;
            font-size: 9px;
            color: #6b7280;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .summary td {
            width: 25%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .summary-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 15px;
            font-weight: bold;
            margin-top: 4px;
            color: #111827;
        }

        table.inventory {
            width: 100%;
            border-collapse: collapse;
        }

        table.inventory th {
            background: #1f2937;
            color: white;
            font-size: 8px;
            padding: 8px 6px;
            text-align: left;
            text-transform: uppercase;
        }

        table.inventory td {
            border-bottom: 1px solid #e5e7eb;
            padding: 7px 6px;
            font-size: 9px;
        }

        table.inventory tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
        }

        .badge-yellow {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-red {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #d1d5db;
            font-size: 8px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- ============================================================
         ENCABEZADO
    ============================================================= --}}

    <div class="header">

        <table class="header-table">

            <tr>

                <td>

                    <div class="title">
                        REPORTE DE INVENTARIO
                    </div>

                    <div class="subtitle">
                        Gestión y Control de Inventario
                    </div>

                </td>

                <td class="date">

                    <strong>Fecha de generación</strong><br>

                    {{ now()->format('d/m/Y H:i') }}

                </td>

            </tr>

        </table>

    </div>


    {{-- ============================================================
         RESUMEN
    ============================================================= --}}

    @php

        $totalArticulos = $articulos->count();

        $unidadesStock = $articulos->sum('stock_actual');

        $valorInventario = $articulos->sum(function ($articulo) {
            return (float) $articulo->stock_actual *
                   (float) $articulo->costo_unitario;
        });

        $articulosAgotados = $articulos->filter(function ($articulo) {
            return $articulo->stock_actual <= 0;
        })->count();

    @endphp


    <table class="summary">

        <tr>

            <td>

                <div class="summary-label">
                    Artículos
                </div>

                <div class="summary-value">
                    {{ number_format($totalArticulos) }}
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Unidades
                </div>

                <div class="summary-value">
                    {{ number_format($unidadesStock) }}
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Valor del inventario
                </div>

                <div class="summary-value">
                    RD$ {{ number_format($valorInventario, 2) }}
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Agotados
                </div>

                <div class="summary-value">
                    {{ number_format($articulosAgotados) }}
                </div>

            </td>

        </tr>

    </table>


    {{-- ============================================================
         TABLA DE INVENTARIO
    ============================================================= --}}

    <table class="inventory">

        <thead>

            <tr>

                <th style="width: 12%;">
                    SKU
                </th>

                <th style="width: 25%;">
                    Artículo
                </th>

                <th style="width: 17%;">
                    Categoría
                </th>

                <th style="width: 10%;" class="text-center">
                    Stock
                </th>

                <th style="width: 10%;" class="text-center">
                    Mínimo
                </th>

                <th style="width: 13%;" class="text-right">
                    Costo
                </th>

                <th style="width: 13%;" class="text-center">
                    Estado
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($articulos as $articulo)

                @php

                    if ($articulo->stock_actual <= 0) {

                        $estado = 'Agotado';
                        $clase = 'badge-red';

                    } elseif ($articulo->stock_actual <= $articulo->minimo_requerido) {

                        $estado = 'Stock bajo';
                        $clase = 'badge-yellow';

                    } else {

                        $estado = 'Disponible';
                        $clase = 'badge-green';

                    }

                @endphp


                <tr>

                    <td>
                        {{ $articulo->codigo_sku }}
                    </td>

                    <td>
                        {{ $articulo->nombre }}
                    </td>

                    <td>
                        {{ $articulo->categoria->nombre ?? 'Sin categoría' }}
                    </td>

                    <td class="text-center">
                        {{ number_format($articulo->stock_actual) }}
                    </td>

                    <td class="text-center">
                        {{ number_format($articulo->minimo_requerido) }}
                    </td>

                    <td class="text-right">
                        RD$ {{ number_format($articulo->costo_unitario, 2) }}
                    </td>

                    <td class="text-center">

                        <span class="badge {{ $clase }}">
                            {{ $estado }}
                        </span>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="text-center">

                        No hay artículos para mostrar.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- ============================================================
         PIE
    ============================================================= --}}

    <div class="footer">

        Sistema de Gestión y Control de Inventario

        <br>

        Reporte generado automáticamente el
        {{ now()->format('d/m/Y H:i:s') }}

    </div>

</body>
</html>