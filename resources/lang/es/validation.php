<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mensajes de Validación
    |--------------------------------------------------------------------------
    */
    'required' => 'El campo :attribute es obligatorio.',
    'numeric'  => 'El campo :attribute debe ser un número.',
    'digits'   => 'El campo :attribute debe tener :digits dígitos.',
    'unique'   => 'Este :attribute ya ha sido registrado.',
    'max'      => [
        'string' => 'El campo :attribute no debe exceder los :max caracteres.',
    ],
    'min'      => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'regex'    => 'El formato del campo :attribute es inválido.',

    /*
    |--------------------------------------------------------------------------
    | Atributos Personalizados (Para que no diga "nombre field" sino "Nombre de la clínica")
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'nombre'        => 'Nombre de la clínica',
        'rfc'           => 'RFC',
        'codigo_postal' => 'Código Postal',
        'telefono'      => 'Teléfono',
        'calle'         => 'Calle',
        'ciudad'        => 'Ciudad / Municipio',
        'estado'        => 'Estado',
        'colonia'       => 'Colonia',
    ],
];