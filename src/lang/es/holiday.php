<?php

return [
    'holiday' => '{1} Feriado|[2,*] Feriados',
    'index' => 'Listado de Feriados',
    'create' => 'Registro de Feriado',
    'edit' => 'Modificar Feriado',
    'detail' => 'Detalle de Feriado #:id: :name',

    'created' => 'Feriado: :name registrado correctamente. No se podran realizar reservaciones de citas en el día :date.',
    'updated' => 'Feriado: :name de ID: :id actualizada correctamente. No se podran realizar reservaciones de citas en el día: :date.',
    'deleted' => 'Feriado de ID: :id eliminada correctamente; Se podran realizar reservaciones de citas en el día: :date.',

    'counter' => '{1} Mostrando :count feriado de un total de :total|[2,*] Mostrando :count feriados de un total de :total',

    'errors' => [
        'not_found' => 'Feriado de ID: :id no encontrado',
        'empty_set' => 'No se encontraron feriados con el criterio ingresado',
        'creation_failed' => 'Error durante el registro del feriado, intente nuevamente o comuniquese con administración.',
        'update_failed' => 'Error durante la actualización del feriado, intente nuevamente o comuniquese con administración.',
    ],
];
