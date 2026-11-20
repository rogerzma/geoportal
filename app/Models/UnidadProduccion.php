<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadProduccion extends Model
{
    use HasFactory;

    protected $table = 'unidad_produccion'; // <--- Agrega esta línea

    protected $fillable = [
        'nombre_up',
        'localidad',
        'responsable',
        'telefono',
        'capturista_id',
        'created_by'        
    ]; // Campos que se pueden asignar masivamente

    // 🔹 Capturista dueño de la UP
    public function capturista()
    {
        return $this->belongsTo(User::class, 'capturista_id');
    }

    // 🔹 Usuario que creó la UP (admin / técnico)
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
