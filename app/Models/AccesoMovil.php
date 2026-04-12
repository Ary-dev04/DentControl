<?php

namespace App\Models;

// 1. Cambiamos el uso de Model por Authenticatable
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AccesoMovil extends Authenticatable
{
    // 2. Añadimos los Traits necesarios para API
    use HasApiTokens, Notifiable;

    protected $table = 'acceso_movil';

    // 🔹 Primary Key personalizada (Verifiqué que usas id_acceso)
    protected $primaryKey = 'id_acceso';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_paciente',
        'usuario_movil',
        'password',
        'token', // Este puedes dejarlo o usar el de Sanctum
        'fecha_expiracion',
        'estatus',
        'fcm_token' // <--- AGREGASTE ESTO AQUÍ
    ];

    protected $hidden = [
        'password',
        'token',
        'remember_token', // Añadido por seguridad
    ];

    protected $casts = [
        'password' => 'hashed',
        'fecha_expiracion' => 'datetime',
    ];

    // Relación: Pertenece a un paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function apiTokens(): MorphMany
    {
        return $this->morphMany(ApiToken::class, 'tokenable');
    }
}