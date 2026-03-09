<?php

return [
    'name' => [
        'required' => 'Debe ingresar un nombre.',
        'min' => 'El nombre debe tener mínimo :min carácteres.',
        'max' => 'El nombre debe tener máximo :max carácteres',
        'unique_clinic' => 'El nombre ingresado ya se encuentra asignado a una clínica.',
        'unique_holiday' => 'El nombre ingresado ya se encuentra registrado como feriado.',
        'unique_treatment' => 'El nombre ingresado ya se encuentra asignado a un tratamiento',
    ],
    'paternal_surname' => [
        'required' => 'Debe ingresar un apellido paterno.',
        'min' => 'El apellido paterno debe tener mínimo :min carácteres.',
        'max' => 'El apellido paterno debe tener máximo :max carácteres',
    ],
    'maternal_surname' => [
        'required' => 'Debe ingresar un apellido materno.',
        'min' => 'El apellido materno debe tener mínimo :min carácteres.',
        'max' => 'El apellido materno debe tener máximo :max carácteres',
    ],
    'dni' => [
        'required' => 'Debe ingresar un DNI.',
        'size' => 'El DNI debe tener entre 8 y 15 digitos.',
        'unique' => 'El DNI ingresado ya se encuentra en uso por otro :entity.',
        'regex' => 'El DNI solo puede contener números.',
    ],
    'birth_date' => [
        'required' => 'Debe ingresar una fecha de nacimiento.',
        'date' => 'La fecha de nacimiento debe tener el formato: AÑO/MES/DÍA.',
        'before' => 'La fecha de nacimiento debe estar en el pasado.',
    ],
    'hired_at' => [
        'required' => 'Debe ingresar una fecha de contratación.',
        'date' => 'La fecha de contratación debe tener el formato: AÑO/MES/DÍA.',
        'before_or_equal' => 'La fecha de contratación debe estar en el pasado.',
    ],
    'manager' => [
        'required' => 'Debe ingresar el nombre del encargado/gerente.',
        'min' => 'El nombre debe tener mínimo 2 carácteres.',
        'max' => 'El nombre debe tener máximo 50 carácteres',
    ],
    'ruc' => [
        'required' => 'Debe ingresar un RUC.',
        'size' => 'El RUC debe tener 11 digitos.',
        'unique' => 'El RUC ingresado ya se encuentra en uso por otro proveedor.',
        'regex' => 'El RUC solo puede contener números y comenzar por 10 o 20.',
    ],
    'address' => [
        'required' => 'Debe ingresar una dirección.',
        'min' => 'La dirección debe tener mínimo 5 carácteres.',
        'max' => 'La dirección debe tener máximo 100 carácteres',
    ],
    'phone' => [
        'required' => 'Debe ingresar un número telefónico.',
        'min' => 'El teléfono debe tener mínimo 6 carácteres.',
        'max' => 'El teléfono debe tener máximo 15 carácteres.',
        'regex' => 'El teléfono solo puede contener números y el símbolo +',
    ],
    'email' => [
        'required' => 'Debe ingresar un correo electrónico válido.',
        'max' => 'El correo electrónico debe tener máximo 50 carácteres.',
        'email' => 'El correo electrónico debe tener el formato: EMAIL@DOMINIO.COM',
        'unique' => 'El correo electrónico ingresado ya se encuentra en uso por otro usuario.',
    ],
    'description' => [
        'required' => 'Debe ingresar una descripción.',
        'min' => 'La descripción debe tener mínimo :min carácteres.',
        'max' => 'La descripción debe tener máximo :max carácteres.',
    ],
    'numeric_value' => [
        'required' => 'Debe ingresar un valor numérico.',
        'numeric' => 'El valor numérico solo puede contener números.',
        'min' => 'El valor numérico debe ser 0.1 como mínimo.',
        'max' => 'El valor numérico debe ser 99999 como máximo.',
        'unique' => 'Ya existe una presentación registrada con los valores ingresados (nombre y valor numérico).',
    ],
    'composition' => [
        'required' => 'Debe ingresar la composición del medicamento.',
        'min' => 'La composición debe tener mínimo :min carácteres.',
        'max' => 'La composición debe tener máximo :max carácteres.',
    ],
    'barcode' => [
        'required' => 'Debe ingresar un código de barras.',
        'min' => 'El código de barras debe tener mínimo :min carácteres.',
        'max' => 'El código de barras debe tener máximo :max carácteres.',
        'regex' => 'El código de barras solo puede contener números y letras.',
        'unique' => 'El código de barras ingresado ya se encuentra asignado a otro medicamento.',
        'not_found' => 'El código de barras ingresado es inválido o no se encuentra registrado en el sistema.',
    ],
    'position' => [
        'required' => 'Debe seleccionar un cargo para el empleado.',
        'in' => 'El cargo seleccionado es inválido.',
    ],
    'clinic_id' => [
        'required' => 'Debe seleccionar un clínica válida.',
        'exists' => 'El clinica seleccionada es inválida.',
    ],
    'role_id' => [
        'required' => 'Debe seleccionar un rol para el empleado.',
        'exists' => 'El rol seleccionada es inválido.',
    ],
    'date' => [
        'required' => 'Debe ingresar una fecha.',
        'date' => 'Formato de fecha incorrecto.',
        'unique_holiday' => 'La fecha ingresada ya se encuentra asignada como feriado.',
        'recurring_date_taken' => 'La fecha ingresada ya se encuentra registrada como un feriado recurrente.',
    ],
    'availabilities' => [
        'required' => 'El doctor debe tener entre 5 y 7 disponibilidades a la semana.',
        'array' => 'El doctor debe tener entre 5 y 7 disponibilidades a la semana.',
        'min' => 'El doctor debe tener mínimo 5 disponibilidades a la semana.',
        'max' => 'El doctor debe tener máximo 7 disponibilidades a la semana.',
        'at_least_one_active' => 'Debe habilitar al menos un (1) día laboral para el doctor.',
        'weekday' => [
            'required' => 'Debe seleccionar un día de la semana.',
            'integer' => 'El día de la semana debe ser un número entre 1 y 7',
            'between' => 'El día de la semana debe estar entre lunes y domingo (1-7)',
        ],
        'start_time' => [
            'required' => 'Debe seleccionar una hora de  inicio.',
            'date_format' => 'Formato de hora incorrecto. (H:M)',
        ],
        'end_time' => [
            'required' => 'Debe seleccionar una hora de fin.',
            'date_format' => 'Formato de hora incorrecto. (H:M)',
            'less_than_start' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'hour_diff' => 'El intervalo entre inicio y fin no puede superar 15 horas.',
        ],
        'break_start' => [
            'required' => 'Debe seleccionar una hora de inicio para el descanso.',
            'date_format' => 'Formato de hora incorrecto. (H:M)',
            'less_than_start' => 'El descanso debe estar dentro del horario de trabajo.',
        ],
        'break_end' => [
            'required' => 'Debe seleccionar una hora de fin para el descanso.',
            'date_format' => 'Formato de hora incorrecto. (H:M)',
            'less_than_start' => 'La hora de fin del descanso debe ser posterior a la hora de inicio.',
            'hour_diff' => 'El descanso no puede superar 2 horas.',
        ],
        'is_active' => [
            'required' => 'Debe definir si la disponibilidad se encuentra activa.',
            'boolean' => 'Este campo solo puede ser verdadero o falso.',
        ],
    ],
    'unavailabilities' => [
        'doctor_id' => [
            'required' => 'Debe seleccionar un doctor.',
            'exists' => 'El doctor seleccionado no existe o no se encuentra disponible.',
        ],
        'start_datetime' => [
            'required' => 'Debe seleccionar una fecha de inicio.',
            'date' => 'Formato de fecha incorrecto.',
        ],
        'end_datetime' => [
            'required' => 'Debe seleccionar una fecha de fin.',
            'date' => 'Formato de fecha incorrecto.',
            'after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'min_gap' => 'La fecha de fin debe ser al menos 24 horas posterior a la fecha de inicio.',
        ],
        'reason' => [
            'required' => 'Debe seleccionar una razón para la indisponibilidad.',
            'enum' => 'La razón seleccionada es inválida.',
        ],
    ],
    'paymentType' => [
        'name' => [
            'required' => 'Debe ingresar un nombre.',
            'min' => 'El nombre debe tener al menos 5 carácteres.',
            'max' => 'El nombre debe tener como máximo 50 carácteres.',
            'unique' => 'El nombre ingresado ya se encuentra asignado a un tipo de pago.',
        ],
        'action' => [
            'required' => 'Debe seleccionar una acción para el tipo de pago.',
            'enum' => 'La acción seleccionada es inválida o no se encuentra disponible.',
        ],
    ],
    'voucherType' => [
        'name' => [
            'required' => 'Debe ingresar un nombre.',
            'min' => 'El nombre debe tener al menos 5 carácteres.',
            'max' => 'El nombre debe tener como máximo 20 carácteres.',
            'unique' => 'El nombre ingresado ya se encuentra asignado a un tipo de comprobante.',
        ],
    ],
    'voucherSerie' => [
        'voucher_type_id' => [
            'required' => 'Debe seleccionar un tipo de comprobante.',
            'exists' => 'El tipo de comprobante seleccionado no existe o no se encuentra disponible.',
        ],
        'serie_number' => [
            'required' => 'Debe ingresar un número de serie.',
            'integer' => 'El número de serie debe ser entero.',
            'min' => 'El número de serie debe ser como mínimo 1.',
            'max' => 'El número de serie debe ser como máximo 999.',
        ],
        'serie' => [
            'required' => 'Debe ingresar un valor numérico para la serie.',
            'regex' => 'Debe ingresar una serie válida del formato: (Primera letra de tipo de comp.)(001-999)',
            'unique' => 'El valor ingresado ya se encuentra asignado a otra serie.',
        ],
        'next_value' => [
            'numeric' => 'Debe ingresar un valor numérico.',
            'min' => 'El valor debe ser como mínimo 1.',
            'max' => 'El valor debe ser como máximo 999.999.999',
        ],
        'is_active' => [
            'boolean' => 'Solo se permite (Verdadero/Falso).',
        ],
    ],
    'price' => [
        'required' => 'Debe ingresar un precio.',
        'numeric' => 'Debe ingresar un valor numérico para el precio.',
        'min' => 'El precio debe ser como mínimo :min.',
        'max' => 'El precio debe ser como máximo :max.',
    ],
    'tax' => [
        'required' => 'Debe ingresar un valor para el IGV.',
        'numeric' => 'Debe ingresar un valor numérico para el IGV.',
        'min' => 'El IGV debe ser como mínimo :min.',
        'max' => 'El IGV debe ser como máximo :max.',
    ],
    'subtotal' => [
        'required' => 'Debe ingresar un valor para el subtotal.',
        'numeric' => 'Debe ingresar un valor numérico para el subtotal.',
        'min' => 'El subtotal debe ser como mínimo :min.',
        'max' => 'El subtotal debe ser como máximo :max.',
    ],
    'total' => [
        'required' => 'Debe ingresar un valor para el total.',
        'numeric' => 'Debe ingresar un valor numérico para el total.',
        'min' => 'El total debe ser como mínimo :min.',
        'max' => 'El total debe ser como máximo :max.',
    ],
    'buy_order_status' => [
        'required' => 'Debe seleccionar un estado para la orden de compra.',
        'enum' => 'El estado seleccionado es inválido.',
    ],
    'buy_order_detail' => [
        'required' => 'Debe seleccionar medicamentos para la orden de compra.',
        'array' => 'Debe seleccionar al menos un (1) medicamento para generar la orden de compra.',
    ],
    'profit' => [
        'required' => 'Debe ingresar la ganancia.',
        'numeric' => 'Debe ingresar un valor numérico para la ganancia.',
        'min' => 'La ganancia debe ser como mínimo :min.',
        'max' => 'La ganancia debe ser como máximo :max.',
        'lte' => 'La ganancia debe ser igual o inferior al precio.',
    ],
    'medicines' => [
        'required' => 'El listado de medicamentos es obligatorio.',
        'array' => 'Los medicamentos deben ser un listado.',
        'barcode_empty' => 'Debe ingresar un código de barras válido.',
        'barcode_not_found' => 'El código de barras ingresado no pertenece a un medicamento registrado.',
        'already_added' => 'El medicamento ya se encuentra asignado al tratamiento.',
    ],
    'medicines.*' => [
        'integer' => 'Cada medicamento debe poseer un ID válido.',
        'distinct' => 'No se permite seleccionar medicamentos duplicados.',
        'exists' => 'El medicamento seleccionado es inválido o no se encuentra disponible.',
    ],
    'supplier_id' => [
        'required' => 'Debe seleccionar un proveedor válido.',
        'exists' => 'El proveedor seleccionado es inválido.',
    ],
    'medicine_id' => [
        'required' => 'Debe seleccionar un medicamento válido.',
        'exists' => 'El medicamento seleccionado es inválido o no se encuentra disponible.',
    ],
    'amount' => [
        'required' => 'Debe ingresar una cantidad.',
        'numeric' => 'El valor de cantidad debe ser numérico.',
        'min' => 'La cantidad debe ser como mínimo :min.',
        'max' => 'La cantidad debe ser como máximo :max.',
    ],
    'unit_price' => [
        'required' => 'Debe ingresar el precio unitario.',
        'numeric' => 'El precio unitario debe ser numérico.',
        'min' => 'El precio unitario debe ser como mínimo :min.',
        'max' => 'El precio unitario debe ser como máximo :max.',
    ],
    'buy_price' => [
        'required' => 'Debe ingresar el precio de compra.',
        'numeric' => 'El precio de compra debe ser numérico.',
        'min' => 'El precio de compra debe ser como mínimo :min.',
        'max' => 'El precio de compra debe ser como máximo :max.',
    ],
    'sell_price' => [
        'required' => 'Debe ingresar el precio de venta.',
        'numeric' => 'El precio de venta debe ser numérico.',
        'min' => 'El precio de venta debe ser como mínimo :min.',
        'max' => 'El precio de venta debe ser como máximo :max.',
        'gte' => 'El precio de venta debe ser igual o superior al precio de compra.',
    ],
    'stock' => [
        'required' => 'Debe ingresar el stock.',
        'numeric' => 'El stock debe ser numérico.',
        'min' => 'El stock debe ser como mínimo :min.',
        'max' => 'El stock debe ser como máximo :max.',
    ],
    'salable' => [
        'required' => 'Debe indicar si el producto se encuentra habilitado para la venta.',
        'boolean' => 'El valor solo puede ser verdadero o falso.',
    ],
];
