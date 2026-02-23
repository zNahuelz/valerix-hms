<?php

return [
    'medicine' => '{1} Medicina|[2,*] Medicinas',
    'index' => 'Listado de Medicinas',
    'create' => 'Registro de Medicina',
    'edit' => 'Modificar Medicina',
    'detail' => 'Detalle de Medicina',

    'created' => 'Medicina: :name registrada correctamente. Asignado el ID: :id',
    'updated' => 'Medicina: :name de ID: :id actualizada correctamente.',
    'is_deleted' => 'La medicina se encuentra eliminada; sus cambios serán visibles al restaurarla.',
    'is_deleted_alt' => 'La medicina se encuentra eliminada, puede restaurarla desde el área de edición.',
    'deleted' => 'Medicina de ID: :id eliminada correctamente.',
    'restored' => 'Medicina de ID: :id restaurada correctamente.',

    'counter' => '{1} Mostrando :count Medicina de un total de :total|[2,*] Mostrando :count medicinas de un total de :total',

    'store_medicine' => 'Registro de Medicina',

    'errors' => [
        'not_found' => 'Medicina de ID: :id no encontrada',
        'not_deleted' => 'La Medicina de ID: :id no se encuentra eliminada',
        'already_deleted' => 'La Medicina de ID: :id ya se encuentra eliminada',
        'empty_set' => 'No se encontraron medicinas con el criterio ingresado',
        'creation_disabled_empty_presentations' => 'El sistema no cuenta con presentaciones de medicinas registradas; el módulo de registro de medicinas se encuentra bloqueado. Debe registrar al menos una (1) presentación para utilizar el módulo con normalidad; comuniquese con administración de ser necesario.',
        'barcode_generation_failed' => 'Ha fallado la generación aleatoria de códigos de barra, intente nuevamente o comuniquese con administración. ',
    ],
];
