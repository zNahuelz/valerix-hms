<?php

return [
    'worker' => '{1} Empleado|[2,*] Empleados',
    'index' => 'Listado de Empleados',
    'create' => 'Registro de Empleado',
    'edit' => 'Modificar Empleado',
    'detail' => 'Detalle de Empleado #:id: :name',

    'created' => 'Empleado: :name registrado correctamente. Asignado el ID: :id',
    'updated' => 'Empleado: :name de ID: :id actualizado correctamente.',
    'is_deleted' => 'El empleado se encuentra eliminado; sus cambios serán visibles al restaurarlo.',
    'is_deleted_alt' => 'El empleado se encuentra eliminado, puede restaurarlo desde el área de edición.',
    'deleted' => 'Empleado de ID: :id eliminado correctamente.',
    'restored' => 'Empleado de ID: :id restaurado correctamente.',

    'counter' => '{1} Mostrando :count empleado de un total de :total|[2,*] Mostrando :count empleados de un total de :total',

    'errors' => [
        'not_found' => 'Empleado de ID: :id no encontrado',
        'not_deleted' => 'El empleado de ID: :id no se encuentra eliminado',
        'already_deleted' => 'El empleado de ID: :id ya se encuentra eliminado',
        'empty_set' => 'No se encontraron empleados con el criterio ingresado',
        'default_worker' => 'El empleado por defecto (DNI: 00000000) no puede ser modificado; comuniquese con administración.',
    ],
];
