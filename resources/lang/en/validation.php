<?php

return [
	'required' => 'El campo :attribute es requerido.',
	'exists' => 'El ID de :modelName no existe.',
	'attributes' => [
		'description' => 'Descripción',
		'short_name' => 'Abreviatura',
		'unit_id' => 'Unidad',
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
	]
];
