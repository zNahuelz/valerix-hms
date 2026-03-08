<?php

return [
    'buy_order' => '{1} Orden de Compra|[2,*] Ordenes de Compra',
    'index' => 'Listado de Ordenes de Compra',
    'create' => 'Registro de Orden de Compra',
    'edit' => 'Modificar Orden de Compra',
    'detail' => 'Detalle de Orden de Compra #:id:',

    'created' => 'Orden de Compra registrada correctamente. Asignado el ID: :id',
    'updated' => 'Orden de Compra de ID: :id actualizada correctamente.',
    'is_deleted' => 'La orden de compra se encuentra eliminada; sus cambios serán visibles al restaurarla.',
    'is_deleted_alt' => 'La orden de compra se encuentra eliminada, puede restaurarla desde el área de edición.',
    'deleted' => 'Orden de Compra de ID: :id eliminada correctamente.',
    'restored' => 'Orden de Compra de ID: :id restaurada correctamente.',

    'counter' => '{1} Mostrando :count orden de compra de un total de :total|[2,*] Mostrando :count ordenes de compra de un total de :total',

    'errors' => [
        'not_found' => 'Orden de Compra de ID: :id no encontrado',
        'not_deleted' => 'La orden de compra de ID: :id no se encuentra eliminada',
        'already_deleted' => 'La orden de compra de ID: :id ya se encuentra eliminada',
        'empty_set' => 'No se encontraron ordenes de compra con el criterio ingresado',
        'creation_disabled_empty_clinics' => 'El módulo de registro de ordenes de compra se encuentra deshabilitado; se requiere al menos una (1) clínica registrada para el correcto funcionamiento del sistema, comuniquese con administración de ser necesario.',
        'creation_disabled_empty_suppliers' => 'El módulo de registro de ordenes de compra se encuentra deshabilitado; se requiere al menos un (1) proveedor registrado para el correcto funcionamiento del módulo, comuniquese con administración de ser necesario.',
        'creation_failed' => 'Error durante el registro de la orden de compra, intente nuevamente o comuniquese con administración.',
        'update_failed' => 'Error durante la actualización de la orden de compra, intente nuevamente o comuniquese con administración.',
    ],
];
