<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
            {
                Schema::table('poligono', function (Blueprint $table) {
                    $table->dropForeign(['up_id']);
                    $table->foreign('up_id')->references('id')->on('unidad_produccion')->onDelete('cascade');
                });
            }

        public function down()
        {
            Schema::table('poligono', function (Blueprint $table) {
                $table->dropForeign(['up_id']);
                $table->foreignId('up_id')->constrained('unidad_produccion')->onDelete('cascade');
            });
        }
};
