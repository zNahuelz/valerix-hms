<?php

return [
    'clinic_medicine' => '{1} Medicina Asignado|[2,*] Medicinas Asignadas',
    'index' => 'Listado de Asignaciones de Medicinas',
    'create' => 'Asignar Medicina',
    'edit' => 'Modificar Asignación de Medicina',
    'detail' => 'Detalle de Asignación de Medicina #:id',

    'created' => 'Medicina: :name asignado correctamente a la clínica: :clinic bajo el ID: :id',
    'updated' => 'Asignación de medicina de ID: :id modificada correctamente.',
    'is_deleted' => 'La asignación de medicina se encuentra eliminada; sus cambios serán visibles al restaurarla.',
    'is_deleted_alt' => 'La asignación de medicina se encuentra eliminada, puede restaurarla desde el área de edición.',
    'deleted' => 'Asignación de medicina de ID: :id eliminada correctamente.',
    'restored' => 'Asignación de medicina de ID: :id restaurada correctamente.',

    'counter' => '{1} Mostrando :count medicina asignado de un total de :total|[2,*] Mostrando :count medicinas asignadas de un total de :total',
    'includes_tax' => 'Incluye IGV',
    'salable_description' => 'Permitir venta',
    'salable' => 'Venta Habilitada',
    'negative_profit_warning' => 'Alerta: La ganancia por venta es inexistente o negativa.',
    'scan_to_search' => 'Escanee un código de barras para comenzar...',
    'already_assigned' => 'Medicina ya asignada',
    'already_assigned_description' => 'La medicina ya se encuentra asignada a la clínica seleccionada, intente nuevamente con otra clínica o modifique la asignación actual.',
    'edit_existing' => 'Modificar Existente',
    'last_sold_by' => 'Último Proveedor',

    'errors' => [
        'not_found' => 'Asignación de medicina de ID: :id no encontrada',
        'not_deleted' => 'La asignación de medicina de ID: :id no se encuentra eliminada',
        'already_deleted' => 'La asignación de medicina de ID: :id ya se encuentra eliminada',
        'empty_set' => 'No se encontraron asignaciones de medicinas con el criterio ingresado',
        'creation_failed' => 'Error durante la asignación de medicina, intente nuevamente o comuniquese con administración.',
        'update_failed' => 'Error durante la modificación de la asignación de medicina, intente nuevamente o comuniquese con administración.',
        'creation_disabled_empty_clinics' => 'El módulo de asignación de medicinas se encuentra deshabilitado; se requiere al menos una (1) clínica registrada para el correcto funcionamiento del sistema; comuniquese con administración.',
        'using_default_tax_rate' => 'ADVERTENCIA: El sistema no cuenta con una configuración de IGV válida, se usara el valor por defecto de 18%.',
    ],
];
