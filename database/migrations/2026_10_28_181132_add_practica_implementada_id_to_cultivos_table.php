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
        Schema::table('poligono', function (Blueprint $table) {
            $table->foreignId('practica_implementada_id')
                ->nullable()
                ->constrained('practicas_implementadas')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poligono', function (Blueprint $table) {
            $table->dropForeign(['practica_implementada_id']);
            $table->dropColumn('practica_implementada_id');
        });
    }
};
