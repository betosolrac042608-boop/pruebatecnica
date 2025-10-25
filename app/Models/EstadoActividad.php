<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoActividad extends Model
{
    use HasFactory;

    protected $table = 'estados_actividad';
    
    public $timestamps = false;

    protected $fillable = [
        'nombre'
    ];

    /**
     * Obtener las actividades que tienen este estado.
     */
    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'estado_id');
    }
}