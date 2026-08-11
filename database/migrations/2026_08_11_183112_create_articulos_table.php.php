// database/migrations/xxxx_xx_xx_create_articulos_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_sku')->unique();
            $table->string('nombre');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->integer('stock_actual')->default(0);
            $table->string('unidad_medida');
            $table->integer('minimo_requerido')->default(0);
            $table->string('ubicacion')->nullable();
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('costo_unitario', 10, 2)->default(0);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};