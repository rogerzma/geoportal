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
            $table->foreignId('variante_cultivo_id')
                ->nullable()
                ->after('cultivo_id')
                ->constrained('variantes_cultivo')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poligono', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'variante_cultivo_id'
            );
        });
    }
};
