<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('poligono', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->json('coordenadas'); // Considera json si lo vas a estructurar en array
            $table->string('cultivo', 255);
            $table->geometry('geom'); // Usa spatial type para PostGIS
            $table->date('fecha_creacion');
            $table->foreignId('up_id')->constrained('unidad_produccion')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poligono');
    }
};
