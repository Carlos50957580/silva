<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Articulo;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@icrapat.com',
            'password' => bcrypt('password'),
        ]);

        // Categorías
        $categorias = [
            ['nombre' => 'Consumibles', 'descripcion' => 'Artículos de consumo diario'],
            ['nombre' => 'Equipos Tecnológicos', 'descripcion' => 'Equipos y dispositivos tecnológicos'],
            ['nombre' => 'Energía', 'descripcion' => 'Equipos relacionados con energía y respaldo'],
            ['nombre' => 'Seguridad', 'descripcion' => 'Equipos y sistemas de seguridad'],
        ];

        foreach ($categorias as $cat) {
            Categoria::create($cat);
        }

        // Artículos
        $articulos = [
            [
                'codigo_sku' => 'RD-INV-001',
                'nombre' => 'Rollo Papel Térmico (80mm)',
                'categoria_id' => 1,
                'stock_actual' => 320,
                'unidad_medida' => 'Rollos',
                'minimo_requerido' => 100,
                'ubicacion' => 'Almacén Principal'
            ],
            [
                'codigo_sku' => 'RD-INV-002',
                'nombre' => 'Lapiceros Bic Azules (Caja 12)',
                'categoria_id' => 1,
                'stock_actual' => 15,
                'unidad_medida' => 'Cajas',
                'minimo_requerido' => 5,
                'ubicacion' => 'Oficina Principal'
            ],
            [
                'codigo_sku' => 'RD-EQP-010',
                'nombre' => 'Terminal POS Sunni V2s Pro',
                'categoria_id' => 2,
                'stock_actual' => 54,
                'unidad_medida' => 'Unidades',
                'minimo_requerido' => 10,
                'ubicacion' => 'Almacén Tecnológico'
            ],
            [
                'codigo_sku' => 'RD-EQP-015',
                'nombre' => 'Impresora Térmica Epson TM-T88',
                'categoria_id' => 2,
                'stock_actual' => 28,
                'unidad_medida' => 'Unidades',
                'minimo_requerido' => 5,
                'ubicacion' => 'Almacén Tecnológico'
            ],
            [
                'codigo_sku' => 'RD-INS-020',
                'nombre' => 'Inversor 3.5KVA (+4 Baterías Trojan)',
                'categoria_id' => 3,
                'stock_actual' => 6,
                'unidad_medida' => 'Sistemas',
                'minimo_requerido' => 2,
                'ubicacion' => 'Almacén Energía'
            ],
            [
                'codigo_sku' => 'RD-SEG-030',
                'nombre' => 'Cámara Vigilancia Hikvision',
                'categoria_id' => 4,
                'stock_actual' => 22,
                'unidad_medida' => 'Unidades',
                'minimo_requerido' => 5,
                'ubicacion' => 'Almacén Seguridad'
            ],
            [
                'codigo_sku' => 'RD-INV-005',
                'nombre' => 'Mascota de Cuadre Diario',
                'categoria_id' => 1,
                'stock_actual' => 12,
                'unidad_medida' => 'Unidades',
                'minimo_requerido' => 20,
                'ubicacion' => 'Oficina Principal'
            ],
        ];

        foreach ($articulos as $art) {
            Articulo::create($art);
        }
    }
}