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
            $table->string('productor')->after('localidad')->nullable();
            $table->dropColumn('responsable_tecnico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unidad_produccion', function (Blueprint $table) {
            $table->string('responsable_tecnico')->nullable();
            $table->dropColumn('productor');
        });
    }
};
