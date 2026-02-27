<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    //
    protected $table = 'citas';

    // Primary key personalizada
    protected $primaryKey = 'id_cita';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_paciente',
        'id_usuario',
        'id_cat_servicio',
        'id_tratamiento',
        'fecha',
        'hora',
        'motivo_consulta'
    ];

    //Relaciones

    // Cita pertenece a un paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    // Cita pertenece a un usuario (dentista)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Cita pertenece a un tratamiento
    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'id_tratamiento', 'id_tratamiento');
    }

    // Cita pertenece a un servicio del catálogo
    public function servicio()
    {
        return $this->belongsTo(CatalogoServicios::class, 'id_cat_servicio', 'id_cat_servicio');
    }
}
