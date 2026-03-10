<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoServicio extends Model
{
    //

    protected $table = 'catalogo_servicios';

    // Primary key personalizada
    protected $primaryKey = 'id_cat_servicio';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_clinica',
        'nombre',
        'descripcion',
        'duracion',
        'precio_sugerido',
        'estatus'
    ];

    //Relaciones
    // Servicio pertenece a una clínica
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'id_clinica', 'id_clinica');
    }

    // Un servicio puede estar en muchas citas
    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_cat_servicio', 'id_cat_servicio');
    }
}
