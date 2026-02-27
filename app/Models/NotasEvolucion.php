<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotasEvolucion extends Model
{
    //
    protected $table = 'notas_evolucion';

    // Primary Key personalizada
    protected $primaryKey = 'id_nota';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_tratamiento',
        'id_usuario',
        'fecha',
        'hora',
        'nota_texto',
        'indicaciones'
    ];

    //Relaciones
    // Pertenece a un tratamiento
    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'id_tratamiento', 'id_tratamiento');
    }

    // Pertenece a un usuario (dentista)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
