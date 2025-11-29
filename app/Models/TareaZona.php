<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaZona extends Model
{
    use HasFactory;

    protected $table = 'tareas_zonas';

    protected $fillable = [
        'predio_id',
        'zona_id',
        'clave',
        'nombre',
        'descripcion',
        'objetivo',
        'tareas_sugeridas',
        'frecuencia',
        'tiempo_minutos',
        'activo',
    ];

    protected $casts = [
        'tiempo_minutos' => 'integer',
        'activo' => 'boolean',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function planAsignaciones()
    {
        return $this->hasMany(PlanTrabajoZonaTarea::class);
    }
}
