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
        Schema::create('unidad_produccion', function (Blueprint $table) {
            $table->id();
            $table->string('propietario');
            $table->string('nombre_up');
            $table->string('localidad');
            $table->string('telefono', 20);
            $table->string('responsable_tecnico');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Llave foránea a users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_produccion');
    }
};
