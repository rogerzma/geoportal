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
        Schema::table('unidad_produccion', function (Blueprint $table) {

            // Productor dueño real
            $table->unsignedBigInteger('productor_id')
                ->nullable()
                ->after('telefono');

            // Usuario que creó la UP (admin / técnico)
            $table->unsignedBigInteger('created_by')
                ->nullable() // 👈 CLAVE
                ->after('productor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unidad_produccion', function (Blueprint $table) {

            // Eliminar llaves foráneas primero
            $table->dropForeign(['productor_id']);
            $table->dropForeign(['created_by']);

            // Eliminar columnas
            $table->dropColumn(['productor_id', 'created_by']);
        });
    }
};
