<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanFotoZona extends Model
{
    use HasFactory;

    protected $table = 'fotos_plan_trabajo';

    protected $fillable = [
        'plan_trabajo_id',
        'zona_id',
        'tipo',
        'ruta',
        'metadata',
        'tomada_en',
    ];

    protected $casts = [
        'metadata' => 'array',
        'tomada_en' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(PlanTrabajoDiario::class, 'plan_trabajo_id');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
