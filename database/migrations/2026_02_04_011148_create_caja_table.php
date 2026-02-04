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
        Schema::create('cajas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('usuario_id')->constrained('users');
    $table->dateTime('fecha_apertura');
    $table->dateTime('fecha_cierre')->nullable();
    $table->decimal('monto_inicial', 10, 2);
    $table->decimal('total_ventas', 10, 2)->default(0);
    $table->decimal('total_efectivo', 10, 2)->default(0);
    $table->decimal('total_tarjeta', 10, 2)->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja');
    }
};
