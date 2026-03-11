<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AccesoMovil extends Model
{
    //
    protected $table = 'acceso_movil';

    // 🔹 Primary Key personalizada
    protected $primaryKey = 'id_acceso';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_paciente',
        'usuario_movil',
        'password',
        'token',
        'fecha_expiracion',
        'estatus'
    ];

    protected $hidden = [
        'password',
        'token'
    ];

    //relacion
    // Pertenece a un paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }


    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
