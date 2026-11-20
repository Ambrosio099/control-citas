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
    Schema::create('citas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('servicio_id')->constrained()->cascadeOnDelete();
        $table->dateTime('fecha_hora')->unique();
        $table->string('estado')->default('pendiente');
        $table->text('notas')->nullable();
        $table->timestamps();
    });
}
};
