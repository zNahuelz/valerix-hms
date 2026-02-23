<?php

return [
    'name' => [
        'required' => 'Debe ingresar un nombre.',
        'min' => 'El nombre debe tener mínimo :min carácteres.',
        'max' => 'El nombre debe tener máximo :max carácteres',
    ],
    'manager' => [
        'required' => 'Debe ingresar el nombre del encargado/gerente.',
        'min' => 'El nombre debe tener mínimo 2 carácteres.',
        'max' => 'El nombre debe tener máximo 50 carácteres',
    ],
    'ruc' => [
        'required' => 'Debe ingresar un RUC.',
        'size' => 'El RUC debe tener 11 digitos.',
        'unique' => 'El RUC ingresado ya se encuentra en uso por otro proveedor.',
        'regex' => 'El RUC solo puede contener números y comenzar por 10 o 20.',
    ],
    'address' => [
        'required' => 'Debe ingresar una dirección.',
        'min' => 'La dirección debe tener mínimo 5 carácteres.',
        'max' => 'La dirección debe tener máximo 100 carácteres',
    ],
    'phone' => [
        'required' => 'Debe ingresar un número telefónico.',
        'min' => 'El teléfono debe tener mínimo 6 carácteres.',
        'max' => 'El teléfono debe tener máximo 15 carácteres.',
        'regex' => 'El teléfono solo puede contener números y el símbolo +',
    ],
    'email' => [
        'required' => 'Debe ingresar un correo electrónico válido.',
        'max' => 'El correo electrónico debe tener máximo 50 carácteres.',
        'email' => 'El correo electrónico debe tener el formato: EMAIL@DOMINIO.COM',
    ],
    'description' => [
        'required' => 'Debe ingresar una descripción.',
        'min' => 'La descripción debe tener mínimo :min carácteres.',
        'max' => 'La descripción debe tener máximo :max carácteres.',
    ],
    'numeric_value' => [
        'required' => 'Debe ingresar un valor numérico.',
        'numeric' => 'El valor numérico solo puede contener números.',
        'min' => 'El valor numérico debe ser 0.1 como mínimo.',
        'max' => 'El valor numérico debe ser 99999 como máximo.',
        'unique' => 'Ya existe una presentación registrada con los valores ingresados (nombre y valor numérico).',
    ],
    'composition' => [
        'required' => 'Debe ingresar la composición del medicamento.',
        'min' => 'La composición debe tener mínimo :min carácteres.',
        'max' => 'La composición debe tener máximo :max carácteres.',
    ],
    'barcode' => [
        'required' => 'Debe ingresar un código de barras.',
        'min' => 'El código de barras debe tener mínimo :min carácteres.',
        'max' => 'El código de barras debe tener máximo :max carácteres.',
        'regex' => 'El código de barras solo puede contener números y letras.',
        'unique' => 'El código de barras ingresado ya se encuentra asignado a otro medicamento.',
    ],
];
