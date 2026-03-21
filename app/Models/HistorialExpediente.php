<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialExpediente extends Model
{
    protected $table = 'historial_expedientes';
    protected $primaryKey = 'id_historial_exp';
    public $timestamps = false; // Usamos fecha_modificacion manualmente

    protected $fillable = [
        'id_paciente',
        'peso',
        'alergias',
        'antecedentes_hereditarios',
        'antecedentes_patologicos',
        'observaciones_generales',
        'id_usuario',
        'fecha_modificacion'
    ];
}