<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccionProgramada extends Model
{
    use HasFactory;

    protected $table = 'acciones_programadas';
    
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tipo_accion_id',
        'nombre_accion',
        'fecha_programada',
        'hora_programada',
        'notas',
        'usuario_id',
        'completed',
        'completed_at',
        'entidad_type',
        'entidad_id'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = 'sch-' . Str::uuid();
            }
        });
    }

    protected $casts = [
        'fecha_programada' => 'date',
        'completed' => 'boolean',
        'completed_at' => 'datetime'
    ];

    /**
     * Obtener el modelo dueño de la acción programada (Animal, Cultivo o Herramienta).
     */
    public function entidad()
    {
        return $this->morphTo();
    }

    /**
     * Obtener el usuario responsable de la acción programada.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Obtener el tipo de acción.
     */
    public function tipoAccion()
    {
        return $this->belongsTo(TipoAccion::class, 'tipo_accion_id');
    }
}