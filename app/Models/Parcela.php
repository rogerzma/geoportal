<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    use HasFactory;

    protected $table = 'parcelas'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id'; // Clave primaria de la tabla

    protected $fillable = [
        'cultivo',
        'coordenadas',
        'geom',
        'nombre_productor',
        'tecnico_id'
    ]; // Campos que se pueden asignar masivamente

    /**
     * Relación con el modelo Tecnico (muchos a uno).
     */
    public function tecnico()
    {
        return $this->belongsTo(Tecnico::class, 'tecnico_id');
    }
}