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
            $table->foreignId('cultivo_id')
                ->nullable()
                ->after('cultivo')
                ->constrained('cultivos')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poligono', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cultivo_id');
        });
    }
};
