<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Predio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'predios';

    protected $fillable = [
        'nombre',
        'codigo',
        'direccion',
        'area_total',
        'descripcion',
        'responsable_id',
        'activo'
    ];

    protected $casts = [
        'area_total' => 'decimal:2',
        'activo' => 'boolean'
    ];

    /**
     * Obtener el usuario responsable del predio.
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Obtener las zonas del predio.
     */
    public function zonas()
    {
        return $this->hasMany(Zona::class);
    }

    /**
     * Obtener las tareas por zona del predio.
     */
    public function tareas()
    {
        return $this->hasMany(TareaZona::class);
    }

    public function planesTrabajo()
    {
        return $this->hasMany(PlanTrabajoDiario::class);
    }
}
