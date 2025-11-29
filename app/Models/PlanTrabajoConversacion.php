<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTrabajoConversacion extends Model
{
    use HasFactory;

    protected $table = 'plan_trabajo_conversacions';

    protected $fillable = [
        'plan_trabajo_id',
        'usuario_id',
        'rol',
        'mensaje',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function planTrabajo()
    {
        return $this->belongsTo(PlanTrabajoDiario::class, 'plan_trabajo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
