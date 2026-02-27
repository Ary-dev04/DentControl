<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoTratamiento extends Model
{
    //
    protected $table = 'catalogo_tratamientos';

    // Primary Key personalizada
    protected $primaryKey = 'id_cat_tratamientos';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_clinica',
        'nombre',
        'descripcion',
        'precio_sugerido',
        'estatus'
    ];

    //Relaciones
    // Pertenece a una clínica
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'id_clinica', 'id_clinica');
    }

    // Un tratamiento del catálogo puede estar en muchos tratamientos aplicados
    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class, 'id_cat_tratamientos', 'id_cat_tratamientos');
    }
}
