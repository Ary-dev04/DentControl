<?php

namespace App\Models;

// 1. Cambiamos el uso de Model por Authenticatable
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
        'estatus'
    ];

    protected $hidden = [
        'password',
        'token',
        'remember_token', // Añadido por seguridad
    ];

    // Relación: Pertenece a un paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }
}