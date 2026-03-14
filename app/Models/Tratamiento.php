<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    //
    protected $table = 'tratamiento';

    // Primary Key personalizada
    protected $primaryKey = 'id_tratamiento';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_paciente',
        'id_usuario',
        'id_clinica',
        'id_cat_tratamientos',
        'diagnostico_inicial',
        'precio_estimado',
        'precio_final',
        'fecha_inicio',
        'fecha_fin',
        'estatus'
    ];

    //Relaciones
    // Pertenece a un paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    // Pertenece a un usuario (dentista)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Pertenece a una clínica
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'id_clinica', 'id_clinica');
    }

    // Pertenece al catálogo de tratamientos
    public function catalogoTratamiento()
    {
        return $this->belongsTo(
            CatalogoTratamiento::class,
            'id_cat_tratamientos',
            'id_cat_tratamientos'
        );
    }

    // Un tratamiento puede tener muchas citas
    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_tratamiento', 'id_tratamiento');
    }

    public function notasEvolucion()
    {
    return $this->hasMany(NotasEvolucion::class, 'id_tratamiento', 'id_tratamiento');
    }

}
