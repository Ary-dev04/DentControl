<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    //
    protected $table = 'paciente';

    protected $primaryKey = 'id_paciente';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_clinica',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'sexo',
        'email',
        'telefono',
        'curp',
        'ocupacion',
        'peso',
        'calle',
        'num_ext',
        'num_int',
        'colonia',
        'ciudad',
        'estado',
        'codigo_postal',
        'estatus',
        'nombre_tutor',
        'parentesco_tutor',
        'telefono_tutor'
    ];
    //Relaciones

    // Paciente pertenece a una clínica
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'id_clinica', 'id_clinica');
    }

    // Paciente tendrá muchas citas (cuando hagas esa migración)
    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_paciente', 'id_paciente');
    }

    // Paciente tendrá un expediente clínico
    public function expediente()
    {
        return $this->hasOne(ExpedienteClinico::class, 'id_paciente', 'id_paciente');
    }

    // Paciente tendrá acceso móvil
    public function accesoMovil()
    {
        return $this->hasOne(AccesoMovil::class, 'id_paciente', 'id_paciente');
    }
}
