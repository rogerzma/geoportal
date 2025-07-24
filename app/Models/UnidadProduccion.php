<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadProduccion extends Model
{
    use HasFactory;

    protected $table = 'unidad_produccion'; // <--- Agrega esta línea

    protected $fillable = [
        'propietario',
        'nombre_up',
        'localidad',
        'telefono',
        'responsable_tecnico',
        'user_id'
    ]; // Campos que se pueden asignar masivamente

    /**
     * Relación: Unidad de Producción pertenece a un usuario (quien la registró)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
