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
            $table->renameColumn('propietario', 'responsable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unidad_produccion', function (Blueprint $table) {
            $table->renameColumn('responsable', 'propietario');
        });
    }
};
