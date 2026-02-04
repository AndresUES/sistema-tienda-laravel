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
       Schema::create('ventas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cliente_id')->nullable()->constrained('clientes');
    $table->foreignId('usuario_id')->constrained('users');
    $table->string('numero_factura');
    $table->date('fecha');

    $table->decimal('subtotal', 10, 2);
    $table->decimal('iva', 10, 2);
    $table->decimal('total', 10, 2);

    $table->enum('tipo_documento', ['CCF', 'FCF', 'TICKET']);
    $table->enum('estado', ['ACTIVA', 'ANULADA'])->default('ACTIVA');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
