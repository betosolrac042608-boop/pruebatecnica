<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'username',
        'rol_id',
        'active',
        'last_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
        'last_login' => 'datetime'
    ];

    /**
     * Obtener el rol del usuario.
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Obtener las herramientas asignadas a este usuario.
     */
    public function herramientasResponsable()
    {
        return $this->hasMany(Herramienta::class, 'responsable_id');
    }

    /**
     * Obtener las actividades asignadas a este usuario.
     */
    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'usuario_id');
    }

    /**
     * Obtener las acciones programadas asignadas a este usuario.
     */
    public function accionesProgramadas()
    {
        return $this->hasMany(AccionProgramada::class, 'usuario_id');
    }

    /**
     * Determinar si el usuario puede acceder al panel de Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->active && $this->email_verified_at !== null;
    }

    /**
     * Verificar si el usuario es administrador.
     */
    public function isAdmin(): bool
    {
        return $this->rol && $this->rol->nombre === 'Administrador';
    }
}
