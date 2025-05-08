<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    use HasFactory;

    protected $table = 'tecnicos'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id'; // Clave primaria de la tabla

    protected $fillable = [
        'nombre',
        'usuario',
        'contraseña'
    ]; // Campos que se pueden asignar masivamente

    /**
     * Relación con el modelo Parcela (uno a muchos).
     */
    public function parcelas()
    {
        return $this->hasMany(Parcela::class, 'tecnico_id');
    }
}