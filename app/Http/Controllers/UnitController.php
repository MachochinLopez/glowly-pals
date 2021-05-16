<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Unit;

class UnitController extends Controller
{
	protected $modelName = 'Unidad';

	/**
	 * Devuelve la lista de todas las unidades.
	 * 
	 * @return json
	 */
	public function index()
	{
		$units = Unit::all();
		return response()->json([
			'data' => $units,
		]);
	}

	/**
	 * Muestra el detalle individual de una unidad.
	 * 
	 * @param int $id Unit Id
	 * @return json
	 */
	public function show($id)
	{
		$unit = Unit::find($id);

		// Si la unidad existe...
		if ($unit) {
			$responseData = [
				'state' => 'success',
				'data' => $unit,
			];
		}
		// Si no...
		else {
			$responseData = [
				'state' => 'error',
				// Devuelve los errores de validación..
				'errors' => [
					__(
						'validation.exists',
						['attribute' => $this->modelName]
					)
				],
			];
		}

		return response()->json($responseData);
	}

	/**
	 * Crea una nueva unidad.
	 * 
	 * @return json
	 */
	public function store()
	{
		$validatedData = $this->validateRequest();

		// Si pasa la validación...
		if ($validatedData['state'] == 'success') {
			$responseData = [
				'state' => $validatedData['state'],
				'message' => __(
					'validation.success_messages.femenine.create',
					['modelName' => $this->modelName]
				),
				// Crea la unidad.
				'data' => Unit::create(request()->all()),
			];
		}
		// Si no...
		else {
			$responseData = [
				'state' => $validatedData['state'],
				// Devuelve los errores de validación..
				'errors' => $validatedData['errors'],
			];
		}

		return response()->json($responseData);
	}

	/**
	 * Actualiza una unidad existente.
	 * 
	 * @param int $id Unit Id
	 * @return json
	 */
	public function update($id)
	{
		$unit = Unit::find($id);

		// Si la unidad existe...
		if ($unit) {
			$validatedData = $this->validateRequest();

			// Si pasa la validación...
			if ($validatedData['state'] == 'success') {
				// Actualiza el registro.
				$unit->update(request()->all());

				$responseData = [
					'state' => $validatedData['state'],
					'message' => __(
						'validation.success_messages.femenine.edit',
						['modelName' => $this->modelName]
					),
					'data' => $unit,
				];
			}
			// Si no...
			else {
				$responseData = [
					'state' => $validatedData['state'],
					// Devuelve los errores de validación..
					'errors' => $validatedData['errors'],
				];
			}
		}
		// Si no...
		else {
			$responseData = [
				'state' => 'error',
				// Devuelve los errores de validación..
				'errors' => [
					__(
						'validation.exists',
						['attribute' => $this->modelName]
					)
				],
			];
		}

		return response()->json($responseData);
	}

	/**
	 * Elimina una unidad.
	 * 
	 * @param int $id Unit Id
	 * @return json
	 */
	public function delete($id)
	{
		$unit = Unit::find($id);

		// Si existe la unidad...
		if ($unit) {
			// La elimina.
			$unit->delete();

			$responseData = [
				'state' => 'success',
				'message' => __(
					'validation.success_messages.femenine.delete',
					['modelName' => $this->modelName]
				)
			];
		}
		// Si no...
		else {
			$responseData = [
				'state' => 'error',
				// Devuelve los errores de validación..
				'errors' => [
					__(
						'validation.exists',
						['attribute' => $this->modelName]
					)
				],
			];
		}

		return response()->json($responseData);
	}

	/**
	 * Valida que el request tenga información
	 * válida.
	 * 
	 * @return array
	 */
	protected function validateRequest()
	{
		// Reglas de validación.
		$rules = [
			'description' => 'required',
			'short_name' => 'required',
		];

		// Validador del request.
		$validator = Validator::make(request()->all(), $rules);
		// Estado de la validación.
		$state = $validator->fails() ? 'error' : 'success';
		// Mensajes de error.
		$errors = $validator->errors()->all();

		return [
			'state' => $state,
			'errors' => $errors,
		];
	}
}
