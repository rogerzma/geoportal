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
        Schema::create('cultivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('nombre_cientifico', 150)->nullable();
            $table->string('categoria', 50);
            $table->string('color', 10)->default('#000000');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            // Agregar el id de quien creo el cultivo
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
                
            $table->unique(['nombre', 'nombre_cientifico']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultivos');
    }
};
