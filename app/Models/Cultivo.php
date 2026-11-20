<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cultivo extends Model
{
    use HasFactory;

    protected $table = 'cultivos';

    protected $fillable = [
        'nombre',
        'nombre_cientifico',
        'categoria',
        'color',
        'activo',
        'created_by',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function variantes()
    {
        return $this->hasMany(
            VarianteCultivo::class,
            'cultivo_id'
        );
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
