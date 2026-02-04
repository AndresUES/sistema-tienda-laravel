<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
    $table->id();
    $table->string('codigo_barra')->nullable();
    $table->string('nombre');
    $table->text('descripcion')->nullable();

    $table->foreignId('categoria_id')->constrained('categorias');
    $table->foreignId('marca_id')->constrained('marcas');

    $table->decimal('precio_compra', 10, 2);
    $table->decimal('precio_venta', 10, 2);

    $table->integer('stock')->default(0);
    $table->integer('stock_minimo')->default(0);

    $table->decimal('iva', 5, 2)->default(13.00); // %

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
