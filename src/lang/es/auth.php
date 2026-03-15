<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'user' => '{1} Usuario|[2,*] Usuarios',
    'welcome' => '¡Bienvenido!',
    'login_title' => 'Inicio de Sesión - Valerix',
    'username' => 'Nombre de usuario',
    'password' => 'Contraseña',
    'forgot_password' => '¿Olvidaste tu contraseña?',
    'remember_me' => 'Recuérdame',
    'login' => 'Iniciar Sesión',
    'login_loading' => 'Iniciando Sesión...',
    'login_success' => '¡Bienvenido de vuelta!',
    'logout' => 'Cerrar Sesión',
    'profile' => 'Mi Perfil',
    'change_password' => 'Cambiar Contraseña',
    'change_avatar' => 'Cambiar Avatar',
    'last_account_change' => 'Ult. Modificación a la Cuenta',
    'role' => '{1} Rol|[2,*] Roles',
    'current_password' => 'Contraseña actual',
    'new_password' => 'Nueva contraseña',
    'confirm_new_password' => 'Confirmar contraseña',
    'failed_attempts' => '¡Ups! Intento de cambio de contraseña fallido, tiene :count intentos restantes.',
    'avatar_preview' => 'Vista previa del avatar',
    'avatar_updated' => 'Avatar actualizado correctamente',
    'check_your_email' => 'Revise su correo electrónico',
    'recovery_email_sent' => 'En caso de que su cuenta este vinculada al correo electrónico ingresado usted recibirá instrucciones para recuperar su cuenta.',
    'recovery_instructions' => 'El siguiente formulario le permite recuperar su cuenta en caso de que haya olvidado su contraseña, ingrese el correo electrónico vinculado a su usuario y espere las instrucciones.',
    'password_recovered' => '¡Bienvenido de vuelta :user! Su contraseña ha sido actualizada correctamente.',
    'reset_password' => 'Recuperación de Contraseña',
    'reset_password_instructions' => 'El siguiente formulario le permite recuperar su cuenta ingresando una nueva contraseña para la misma; si ústed no solicito el cambio puede salir de esta página.',

    'errors' => [
        'invalid_credentials' => 'Usuario o contraseña incorrecto',
        'invalid_token' => 'Token inválido o expirado',
        'missing_permission' => 'Su cuenta no posee los permisos necesarios para completar la operación',
        'missing_permissions' => 'Su cuenta no posee acceso al módulo seleccionado',
        'account_locked' => 'Su cuenta se encuentra bloqueada debido a demasiados intentos de inicio de sesión fallidos. Puede volver a intentar nuevamente el: :lockedUntil',
        'session_expired' => 'Su sesión ha expirado; debe volver a iniciar sesión',
        'server_error' => 'Error interno del servidor, vuelva a intentarlo más tarde',
        'change_password_failed' => '¡Ups! Ha sucedido un error durante el cambio de contraseña, intente nuevamente o comuniquese con administración.',
        'change_avatar_failed' => 'Ha sucedido un error durante al cambio de foto de perfil, intente nuevamente o comuniquese con administración.',
    ],

    'validation' => [
        'username' => [
            'required' => 'Debe ingresar su nombre de usuario.',
            'max' => 'El usuario debe tener entre 5 y 20 caracteres.',
        ],
        'password' => [
            'required' => 'Debe ingresar su contraseña.',
            'max' => 'La contraseña debe tener entre 5 y 20 caracteres.',
        ],
    ],
];
