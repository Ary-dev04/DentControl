<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinica extends Model
{
    //
    protected $table = 'clinica';

    protected $primaryKey = 'id_clinica';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'rfc',
        'calle',
        'numero_ext',
        'numero_int',
        'colonia',
        'ciudad',
        'estado',
        'codigo_postal',
        'telefono',
        'logo_ruta',
        'estatus'
    ];

    protected $casts = [
    //'rfc' => 'encrypted', 
    ];

    // Relaciones
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_clinica', 'id_clinica');
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'id_clinica', 'id_clinica');
    }

    public function citas()
    {
        // Esto le dice a Laravel: "Una clínica tiene muchas citas"
        // 'id_clinica' es la llave foránea en la tabla 'citas'
        return $this->hasMany(Cita::class, 'id_clinica', 'id_clinica');
    }
}
