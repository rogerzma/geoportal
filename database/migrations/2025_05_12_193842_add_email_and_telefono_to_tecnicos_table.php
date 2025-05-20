<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailAndTelefonoToTecnicosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tecnicos', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('usuario'); // Permitir valores NULL temporalmente
            $table->string('telefono')->nullable()->after('email'); // Permitir valores NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tecnicos', function (Blueprint $table) {
            $table->dropColumn('email'); // Eliminar el campo email
            $table->dropColumn('telefono'); // Eliminar el campo telefono
        });
    }
}