<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteClinico extends Model
{
    //
    protected $table = 'expediente_clinico';

    // Primary Key personalizada
    protected $primaryKey = 'id_expediente';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_paciente',
        'antecedentes_hereditarios',
        'antecedentes_patologicos',
        'alergias',
        'observaciones_generales',
        'fecha_registro'
    ];

    //Relacion
    // Pertenece a un paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }
}
