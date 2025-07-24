<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pozos extends Model
{
    use HasFactory;

    protected $fillable = [
        'gasto',
        'profundidad',
        'up_id', // id de la unidad de produccion
        'user_id' // id del usuario que crea el pozo
    ]; // Campos que se pueden asignar masivamente
}
