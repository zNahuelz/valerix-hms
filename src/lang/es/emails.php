<?php

return [
    'recoveryEmail' => [
        'subject' => 'Valerix HCSM - Recuperación de Cuenta',
        'greeting' => '¡Hola :name!',
        'line_1' => 'Has recibido este correo electrónico porque recibimos una solicitud de recuperación de contraseña para tu cuenta.',
        'action' => 'Recuperar Cuenta',
        'salutation' => 'Saludos, Valerix HCSM - Sistemas',
        'line_2' => 'Este enlace expira en una hora.',
        'line_3' => 'Si no has solicitado la recuperación de tu cuenta puedes ignorar este mensaje.',
    ],
    'button_trouble' => 'Si tienes problemas con el botón de ":actionText", copia y pega el siguiente enlace en tu navegador web:',
    'all_rights_reserved' => 'Todos los derechos reservados',
    'welcome' => [
        'subject' => 'Valerix HCSM - Bienvenida al Sistema',
        'greeting' => '¡Hola :fullName!',
        'line_1' => "Te damos la bienvenida al centro de salud; a continuación te indicamos las credenciales de acceso a tu cuenta del sistema Valerix HCSM.\n",
        'username_line' => "**Nombre de Usuario:** :username\n",
        'password_line' => "**Contraseña:** :password\n",
        'action' => 'Acceder al Sistema',
        'salutation' => 'Saludos, Valerix HCSM - Sistemas',
    ],
    'password_updated' => [
        'subject' => 'Valerix HCSM - Contraseña actualizada',
        'greeting' => '¡Hola :username!',
        'line_1' => "La contraseña de tu cuenta de acceso al sistema ha sido modificada el día: :fullDate.\n",
        'line_2' => 'Si esto es un error debes comunicarte con administración de inmediato.',
        'line_3' => "**Contacto:** +51 999-999-999\n",
        'salutation' => 'Saludos, Valerix HCSM - Sistemas',
    ],
    'email_updated' => [
        'subject' => 'Valerix HCSM - Correo Electrónico Actualizado',
        'greeting' => '¡Hola :username!',
        'line_1' => "El correo electrónico vinculado a tu cuenta ha sido modificada el día: :fullDate.\n",
        'line_2' => 'Si esto es un error debes comunicarte con administración de inmediato.',
        'line_3' => "**Contacto:** +51 999-999-999\n",
        'salutation' => 'Saludos, Valerix HCSM - Sistemas',
    ],
];
