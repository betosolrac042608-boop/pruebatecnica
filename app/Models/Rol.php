<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    public $timestamps = false;
    
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    /**
     * Obtener los usuarios que tienen este rol.
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}