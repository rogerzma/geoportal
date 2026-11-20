<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variantes_cultivo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cultivo_id')
                ->constrained('cultivos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('nombre', 150);

            $table->timestamps();

            // Impide repetir la misma variante dentro del mismo cultivo.
            $table->unique(
                ['cultivo_id', 'nombre'],
                'variantes_cultivo_cultivo_nombre_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variantes_cultivo');
    }
};