<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAccion extends Model
{
    use HasFactory;

    protected $table = 'tipos_accion';
    
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    /**
     * Obtener las actividades que usan este tipo de acción.
     */
    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'tipo_accion_id');
    }

    /**
     * Obtener las acciones programadas que usan este tipo de acción.
     */
    public function accionesProgramadas()
    {
        return $this->hasMany(AccionProgramada::class, 'tipo_accion_id');
    }
}