<?php

return [
    'user' => '{1} Usuario|[2,*] Usuarios',
    'index' => 'Listado de Usuarios',
    'create' => 'Registro de Usuario',
    'edit' => 'Modificar Usuario',
    'detail' => 'Detalle de Usuario #:id: :username',

    'created' => 'Usuario: :username registrado correctamente. Asignado el ID: :id y usuario :username; sus credenciales de acceso serán enviadas a su correo electrónico en los próximos 5 minutos.',
    'updated' => 'Usuario: :username de ID: :id actualizado correctamente. El Usuario será notificado acerca de estos cambios vía correo electrónico en los próximos 5 minutos.',
    'is_deleted' => 'El usuario se encuentra eliminado; sus cambios serán visibles al restaurarlo.',
    'is_deleted_alt' => 'El usuario se encuentra eliminado, puede restaurarlo desde el área de edición.',
    'deleted' => 'Usuario de ID: :id eliminado correctamente; su cuenta de usuario ha sido deshabilitada.',
    'restored' => 'Usuario de ID: :id restaurado correctamente; su cuenta de usuario ha sido habilitada.',
    'username_generation' => 'Posterior al registro del Usuario, se generará una cuenta de usuario para su uso; el nombre de usuario será generado en base a su DNI y las credenciales de acceso serán enviadas a su correo electrónico.',
    'editing_user' => 'Modificando el usuario :username de correo electrónico: :email',

    'counter' => '{1} Mostrando :count usuario de un total de :total|[2,*] Mostrando :count usuarios de un total de :total',

    'errors' => [
        'not_found' => 'Usuario de ID: :id no encontrado',
        'not_deleted' => 'El usuario de ID: :id no se encuentra eliminado',
        'already_deleted' => 'El usuario de ID: :id ya se encuentra eliminado',
        'empty_set' => 'No se encontraron usuarios con el criterio ingresado',
        'default_user' => 'El usuario por defecto (DNI: 00000000) no puede ser modificado; comuniquese con administración.',
        'creation_disabled_empty_roles' => 'El sistema no cuenta con roles registrados; el módulo de registro de Usuarios se encuentra bloqueado. Debe registrar al menos un (1) rol para utilizar el sistema con normalidad; comuniquese con administración de ser necesario.',
        'editing_session' => '¡Ups! Solo puedes modificar las propiedades de tu usuario desde tu perfil.',
        'editing_nurse' => '¡Ups! Solo puedes modificar las propiedades de una enfermera desde la sección correspondiente.',
        'editing_doctor' => '¡Ups! Solo puedes modificar las propiedades de una enfermera desde la sección correspondiente.',
        'creation_failed' => 'Error durante el registro del usuario, intente nuevamente o comuniquese con administración.',
        'update_failed' => 'Error durante la actualización del usuario, intente nuevamente o comuniquese con administración.',
    ],
];
