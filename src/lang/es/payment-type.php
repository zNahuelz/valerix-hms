<?php

return [
    'payment_type' => '{1} Tipo de pago|[2,*] Tipo de pagos',
    'index' => 'Listado de Tipo de pagos',
    'create' => 'Registro de Tipo de pago',
    'edit' => 'Modificar Tipo de pago',
    'detail' => 'Detalle de Tipo de pago #:id: :name',

    'created' => 'Tipo de pago: :name registrado correctamente. Asignado el ID: :id',
    'updated' => 'Tipo de pago: :name de ID: :id actualizado correctamente.',
    'is_deleted' => 'El tipo de pago se encuentra eliminado; sus cambios serán visibles al restaurarlo.',
    'is_deleted_alt' => 'El tipo de pago se encuentra eliminado, puede restaurarlo desde el área de edición.',
    'deleted' => 'Tipo de pago de ID: :id eliminado correctamente.',
    'restored' => 'Tipo de pago de ID: :id restaurado correctamente.',

    'counter' => '{1} Mostrando :count tipo de pago de un total de :total|[2,*] Mostrando :count tipos de pago de un total de :total',
    'info' => 'El sistema posee diversos tipos de pago cuyos comportamientos se clasifican en:
    - DIGITAL: Durante el proceso de registro de venta,  solicitarán el ingreso de un HASH o comprobante de pago de pago, el cual es generado por el punto de venta o aplicativo del servicio digital de pago del cliente y no requieren el ingreso del monto de pago (efectivo entregado por el usuario).

    - EFECTIVO: No requieren HASH de pago y solo necesitan el monto de dinero en efectivo entregado por el cliente.',

    'errors' => [
        'not_found' => 'Tipo de pago de ID: :id no encontrado',
        'not_deleted' => 'El tipo de pago de ID: :id no se encuentra eliminado',
        'already_deleted' => 'El tipo de pago de ID: :id ya se encuentra eliminado',
        'empty_set' => 'No se encontraron tipos de pago con el criterio ingresado',
        'creation_failed' => 'Error durante el registro del tipo de pago, intente nuevamente o comuniquese con administración.',
        'update_failed' => 'Error durante la actualización del tipo de pago, intente nuevamente o comuniquese con administración.',
    ],
];
