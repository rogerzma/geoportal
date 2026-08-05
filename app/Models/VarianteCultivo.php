<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VarianteCultivo extends Model
{
    use HasFactory;
    protected $table = 'variantes_cultivo';

    protected $fillable = [
        'cultivo_id',
        'nombre'
    ];

    // Cultivo al que pertenece la variante.
    public function cultivo()
    {
        return $this->belongsTo(Cultivo::class, 'cultivo_id');
    }
}
