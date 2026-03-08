<?php

return [
    'role' => '{1} Rol|[2,*] Roles',
    'index' => 'Listado de Roles',
    'create' => 'Registro de Rol',
    'edit' => 'Modificar Rol',
    'detail' => 'Detalle de Rol #:id: :name',

    'created' => 'Rol: :name registrado correctamente. Asignado el ID: :id',
    'updated' => 'Rol: :name de ID: :id actualizado correctamente.',
    'is_deleted' => 'El rol se encuentra eliminado; sus cambios serán visibles al restaurarlo.',
    'is_deleted_alt' => 'El rol se encuentra eliminado, puede restaurarlo desde el área de edición.',
    'deleted' => 'Rol de ID: :id eliminado correctamente.',
    'restored' => 'Rol de ID: :id restaurado correctamente.',

    'counter' => '{1} Mostrando :count rol de un total de :total|[2,*] Mostrando :count roles de un total de :total',

    'errors' => [
        'not_found' => 'Rol de ID: :id no encontrado',
        'not_deleted' => 'El rol de ID: :id no se encuentra eliminado',
        'already_deleted' => 'El rol de ID: :id ya se encuentra eliminado',
        'empty_set' => 'No se encontraron roles con el criterio ingresado',
        'creation_failed' => 'Error durante el registro del rol, intente nuevamente o comuniquese con administración.',
        'update_failed' => 'Error durante la actualización del rol, intente nuevamente o comuniquese con administración.',
    ],
];
