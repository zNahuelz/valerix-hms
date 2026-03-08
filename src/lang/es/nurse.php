<?php

return [
    'nurse' => '{1} Enfermera|[2,*] Enfermeras',
    'index' => 'Listado de Enfermeras',
    'create' => 'Registro de Enfermera',
    'edit' => 'Modificar Enfermera',
    'detail' => 'Detalle de Enfermera #:id: :name',

    'created' => 'Enfermera: :name registrado correctamente. Asignado el ID: :id y usuario :username; sus credenciales de acceso serán enviadas a su correo electrónico en los próximos 5 minutos.',
    'updated' => 'Enfermera: :name de ID: :id actualizada correctamente. El enfermera será notificado acerca de estos cambios vía correo electrónico en los próximos 5 minutos.',
    'is_deleted' => 'El enfermera se encuentra eliminada; sus cambios serán visibles al restaurarlo.',
    'is_deleted_alt' => 'El enfermera se encuentra eliminada, puede restaurarlo desde el área de edición.',
    'deleted' => 'Enfermera de ID: :id eliminada correctamente; su cuenta de usuario ha sido deshabilitada.',
    'restored' => 'Enfermera de ID: :id restaurado correctamente; su cuenta de usuario ha sido habilitada.',
    'username_generation' => 'Posterior al registro del enfermera, se generará una cuenta de usuario para su uso; el nombre de usuario será generado en base a su DNI y las credenciales de acceso serán enviadas a su correo electrónico.',

    'counter' => '{1} Mostrando :count enfermera de un total de :total|[2,*] Mostrando :count enfermeras de un total de :total',

    'errors' => [
        'not_found' => 'Enfermera de ID: :id no encontrado',
        'not_deleted' => 'El enfermera de ID: :id no se encuentra eliminada',
        'already_deleted' => 'El enfermera de ID: :id ya se encuentra eliminada',
        'empty_set' => 'No se encontraron enfermeras con el criterio ingresado',
        'creation_disabled_empty_clinics' => 'El sistema no cuenta con clínicas registradas; el módulo de registro de enfermeras se encuentra bloqueado. Debe registrar al menos una (1) clínica para utilizar el sistema con normalidad; comuniquese con administración de ser necesario.',
        'creation_disabled_empty_roles' => 'El sistema no cuenta con roles registrados; el módulo de registro de enfermeras se encuentra bloqueado. Debe registrar al menos un (1) rol para utilizar el sistema con normalidad; comuniquese con administración de ser necesario.',
        'creation_disabled_nurse_role_not_found' => 'El sistema no cuenta con el rol de enfermera registrado; el módulo de registro de enfermeras se encuentra bloqueado. Debe registrar un rol llamado "ENFERMERA" y asignarle los permisos correspondiente; comuniquese con administración de ser necesario.',
        'editing_session' => '¡Ups! Solo puedes modificar las propiedades de tu usuario desde tu perfil.',
        'editing_nurse' => '¡Ups! Solo puedes modificar las propiedades de una enfermera desde la sección correspondiente.',
        'creation_failed' => 'Error durante el registro de la enfermera, intente nuevamente o comuniquese con administración.',
        'update_failed' => 'Error durante la actualización de la enfermera, intente nuevamente o comuniquese con administración.',
    ],
];
