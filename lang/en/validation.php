<?php
return [
    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'email'                => 'El campo :attribute debe ser una dirección de correo válida.',
    'max'                  => [
        'string'  => 'El campo :attribute no debe ser mayor a :max caracteres.',
    ],
    'min'                  => [
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'password' => [
        'letters' => 'La contraseña debe contener al menos una letra.',
        'mixed'   => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
        'numbers' => 'La contraseña debe contener al menos un número.',
        'symbols' => 'La contraseña debe contener al menos un símbolo.',
    ],
    'required'             => 'El campo :attribute es obligatorio.',
    'unique'               => 'El campo :attribute ya ha sido registrado.',
    
    'custom' => [
        'email' => [
            'unique' => 'Este correo electrónico ya está en uso. Por favor, intente con otro.',
        ],
    ],
    
    'attributes' => [
        'name'     => 'nombre',
        'password' => 'contraseña',
    ],
];