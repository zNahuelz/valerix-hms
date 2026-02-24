<?php

return [
    'patient' => '{1} Paciente|[2,*] Pacientes',
    'index' => 'Listado de Pacientes',
    'create' => 'Registro de Paciente',
    'edit' => 'Modificar Paciente',
    'detail' => 'Detalle de Paciente #:id: :name',

    'created' => 'Paciente: :name registrado correctamente. Asignado el ID: :id',
    'updated' => 'Paciente: :name de ID: :id actualizado correctamente.',
    'is_deleted' => 'El paciente se encuentra eliminado; sus cambios serán visibles al restaurarlo.',
    'is_deleted_alt' => 'El paciente se encuentra eliminado, puede restaurarlo desde el área de edición.',
    'deleted' => 'Paciente de ID: :id eliminado correctamente.',
    'restored' => 'Paciente de ID: :id restaurado correctamente.',

    'counter' => '{1} Mostrando :count paciente de un total de :total|[2,*] Mostrando :count pacientes de un total de :total',

    'errors' => [
        'not_found' => 'Paciente de ID: :id no encontrado',
        'not_deleted' => 'El paciente de ID: :id no se encuentra eliminado',
        'already_deleted' => 'El paciente de ID: :id ya se encuentra eliminado',
        'empty_set' => 'No se encontraron pacientes con el criterio ingresado',
        'default_patient' => 'El paciente por defecto (DNI: 00000000) no puede ser modificado; comuniquese con administración.',
    ],
];
