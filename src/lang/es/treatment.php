<?php

return [
    'treatment' => '{1} Tratamiento|[2,*] Tratamientos',
    'index' => 'Listado de Tratamientos',
    'create' => 'Registro de Tratamiento',
    'edit' => 'Modificar Tratamiento',
    'detail' => 'Detalle de Tratamiento #:id: :name',

    'created' => 'Tratamiento: :name registrado correctamente. Asignado el ID: :id',
    'updated' => 'Tratamiento: :name de ID: :id actualizado correctamente.',
    'is_deleted' => 'El tratamiento se encuentra eliminado; sus cambios serán visibles al restaurarlo.',
    'is_deleted_alt' => 'El tratamiento se encuentra eliminado, puede restaurarlo desde el área de edición.',
    'deleted' => 'Tratamiento de ID: :id eliminado correctamente.',
    'restored' => 'Tratamiento de ID: :id restaurado correctamente.',

    'counter' => '{1} Mostrando :count Tratamiento de un total de :total|[2,*] Mostrando :count tratamientos de un total de :total',
    'selected_medicines' => 'Medicamentos seleccionados: :count',

    'errors' => [
        'not_found' => 'Tratamiento de ID: :id no encontrado',
        'not_deleted' => 'El Tratamiento de ID: :id no se encuentra eliminado',
        'already_deleted' => 'El Tratamiento de ID: :id ya se encuentra eliminado',
        'empty_set' => 'No se encontraron tratamientos con el criterio ingresado',
        'medicine_trashed' => 'El medicamento: :name de ID: :id se encuentra eliminado.',
        'no_medicines' => 'Sin medicamentos asignados',
        'no_medicines_hint' => 'Escanee el código de barras de medicamentos para añadirlos como requisitos para el tratamiento',
    ],
];
