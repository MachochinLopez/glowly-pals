<?php

return [
	'required' => 'El campo :attribute es requerido.',
	'exists' => 'El ID de :attribute no existe.',
	'min' => [
		'numeric' => 'La :attribute debe ser de al menos :min.'
	],
	'min_amount_error' => 'La cantidad a la que intenta dar salida supera la cantidad de existencias en el inventario. Cantidad actual: :current_amount',
	'integer' => 'El campo :attribute debe contener un valor numérico.',
	'attributes' => [
		'description' => 'Descripción',
		'short_name' => 'Abreviatura',
		'unit_id' => 'Unidad',
		'user_id' => 'Usuario',
		'quantity' => 'Cantidad',
		'deposit_id' => 'Depósito',
		'product_id' => 'Producto',
	],
	'success_messages' => [
		'masculine' => [
			'create' => ':modelName creado exitosamente.',
			'edit' => ':modelName editado exitosamente.',
			'delete' => ':modelName eliminado exitosamente.',
		],
		'femenine' => [
			'create' => ':modelName creada exitosamente.',
			'edit' => ':modelName editada exitosamente.',
			'delete' => ':modelName eliminada exitosamente.',
		]
	],
	'entry' => 'Entrada exitosa. Inventario actualizado.',
	'exit' => 'Salida exitosa. Inventario actualizado.'
];
