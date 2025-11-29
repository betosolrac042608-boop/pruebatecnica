<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTrabajoLog extends Model
{
    use HasFactory;

    protected $table = 'plan_trabajo_logs';

    protected $fillable = [
        'plan_trabajo_id',
        'request_payload',
        'response_payload',
        'status',
        'error',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function plan()
    {
        return $this->belongsTo(PlanTrabajoDiario::class, 'plan_trabajo_id');
    }
}
