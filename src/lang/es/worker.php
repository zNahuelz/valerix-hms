<?php

return [
    'worker' => '{1} Empleado|[2,*] Empleados',
    'index' => 'Listado de Empleados',
    'create' => 'Registro de Empleado',
    'edit' => 'Modificar Empleado',
    'detail' => 'Detalle de Empleado #:id: :name',

    'created' => 'Empleado: :name registrado correctamente. Asignado el ID: :id y usuario :username; sus credenciales de acceso serán enviadas a su correo electrónico en los próximos 5 minutos.',
    'updated' => 'Empleado: :name de ID: :id actualizado correctamente. El empleado será notificado acerca de estos cambios vía correo electrónico en los próximos 5 minutos.',
    'is_deleted' => 'El empleado se encuentra eliminado; sus cambios serán visibles al restaurarlo.',
    'is_deleted_alt' => 'El empleado se encuentra eliminado, puede restaurarlo desde el área de edición.',
    'deleted' => 'Empleado de ID: :id eliminado correctamente; su cuenta de usuario ha sido deshabilitada.',
    'restored' => 'Empleado de ID: :id restaurado correctamente; su cuenta de usuario ha sido habilitada.',
    'username_generation' => 'Posterior al registro del empleado, se generará una cuenta de usuario para su uso; el nombre de usuario será generado en base a su DNI y las credenciales de acceso serán enviadas a su correo electrónico.',

    'counter' => '{1} Mostrando :count empleado de un total de :total|[2,*] Mostrando :count empleados de un total de :total',

    'errors' => [
        'not_found' => 'Empleado de ID: :id no encontrado',
        'not_deleted' => 'El empleado de ID: :id no se encuentra eliminado',
        'already_deleted' => 'El empleado de ID: :id ya se encuentra eliminado',
        'empty_set' => 'No se encontraron empleados con el criterio ingresado',
        'default_worker' => 'El empleado por defecto (DNI: 00000000) no puede ser modificado; comuniquese con administración.',
        'creation_disabled_empty_clinics' => 'El sistema no cuenta con clínicas registradas; el módulo de registro de empleados se encuentra bloqueado. Debe registrar al menos una (1) clínica para utilizar el sistema con normalidad; comuniquese con administración de ser necesario.',
        'creation_disabled_empty_roles' => 'El sistema no cuenta con roles registrados; el módulo de registro de empleados se encuentra bloqueado. Debe registrar al menos un (1) rol para utilizar el sistema con normalidad; comuniquese con administración de ser necesario.',
        'editing_session' => '¡Ups! Solo puedes modificar las propiedades de tu usuario desde tu perfil.',
        'editing_nurse' => '¡Ups! Solo puedes modificar las propiedades de una enfermera desde la sección correspondiente.',
        'creation_failed' => 'Error durante el registro del empleado, intente nuevamente o comuniquese con administración.',
        'update_failed' => 'Error durante la actualización del empleado, intente nuevamente o comuniquese con administración.',
    ],
];
