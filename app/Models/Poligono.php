<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\UnidadProduccion;
use App\Models\Cultivo;

class Poligono extends Model
{
    use HasFactory;

    protected $table = 'poligono'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id'; // Clave primaria de la tabla

    protected $fillable = [
        'nombre',
        'coordenadas',
        'cultivo',
        'cultivo_id', //Variable temporal para relacionarlo con la tabla cultivos
        'variante_cultivo_id', // Variable temporal para relacionarlo con la tabla variantes_cultivo
        'geom',
        'fecha_creacion',
        'up_id', //id de la unidad de produccion
        'user_id' //id del usuario que crea el poligono
    ]; // Campos que se pueden asignar masivamente

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function unidadProduccion()
    {
        return $this->belongsTo(UnidadProduccion::class, 'up_id');
    }

    public function cultivoCatalogo()
    {
        return $this->belongsTo(
            Cultivo::class,
            'cultivo_id'
        );
    }

    public function varianteCultivo()
    {
        return $this->belongsTo(
            VarianteCultivo::class,
            'variante_cultivo_id'
        );
    }
}
