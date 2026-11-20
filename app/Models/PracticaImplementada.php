<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaImplementada extends Model
{
    use HasFactory;

    protected $table = 'practicas_implementadas';
    protected $fillable = [
        'clave',
        'nombre',
        'activo'
    ];

    public function poligonos(){
        return $this->hasMany(Poligono::class, 'practica_implementada_id');
    }
}
