<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    //
    use Notifiable;
    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_clinica',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'email', 'telefono',
        'cedula_profesional',
        'nom_usuario',
        'password',
        'rol',
        'estatus'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    'password' => 'hashed', // Laravel se encarga de encriptar al guardar
    ];

    // Relaciones
    // Usuario pertenece a una clínica
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'id_clinica', 'id_clinica');
    }

    // Usuario tiene muchas citas (cuando hagas esa migración)
    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_usuario', 'id_usuario');
    }

    // Usuario escribe muchas notas
    public function notasEvolucion()
    {
        return $this->hasMany(NotasEvolucion::class, 'id_usuario', 'id_usuario');
    }


    public function getAuthPassword()
{
    return $this->password; // Asegura que use tu columna password
}
    public function getAuthIdentifierName()
    {
    return 'id_usuario';
    }
}
