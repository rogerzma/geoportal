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
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id(); // ID único de la parcela
            $table->string('cultivo'); // Tipo de cultivo
            $table->text('coordenadas'); // Coordenadas en formato texto
            $table->geometry('geom'); // Geometría de la parcela (requiere PostGIS)
            $table->string('nombre_productor'); // Nombre del productor
            $table->unsignedBigInteger('tecnico_id'); // Relación con la tabla tecnicos
            $table->timestamps(); // Campos created_at y updated_at

            // Llave foránea para relacionar con la tabla tecnicos
            $table->foreign('tecnico_id')->references('id')->on('tecnicos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelas');
    }
};