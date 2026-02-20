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

    'errors' => [
        'invalid_credentials' => 'Usuario o contraseña incorrecto',
        'invalid_token' => 'Token inválido o expirado',
        'missing_permission' => 'Su cuenta no posee los permisos necesarios para completar la operación',
        'missing_permissions' => 'Su cuenta no posee acceso al módulo seleccionado',
        'account_locked' => 'Su cuenta se encuentra bloqueada debido a demasiados intentos de inicio de sesión fallidos. Puede volver a intentar nuevamente el: :lockedUntil',
        'session_expired' => 'Su sesión ha expirado; debe volver a iniciar sesión',
        'server_error' => 'Error interno del servidor, vuelva a intentarlo más tarde',
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
