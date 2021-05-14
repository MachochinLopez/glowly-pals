<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Unit;

class UnitController extends Controller
{
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
				// Crea la unidad.
				'data' => Unit::create(request()->all()),
			];
		}
		// Si no...
		else {
			$responseData = [
				'state' => $validatedData['state'],
				// Crea la unidad.
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
				$unit->update($this->validateRequest());
				$responseData = [
					'state' => $validatedData['state'],
					// Crea la unidad.
					'data' => $unit,
				];
			}
			// Si no...
			else {
				$responseData = [
					'state' => $validatedData['state'],
					// Crea la unidad.
					'errors' => $validatedData['errors'],
				];
			}
		}
		// Si no...
		else {
			$responseData = [
				'state' => 'error',
				// Crea la unidad.
				'errors' => [
					__('validation.not_exist', ['id' => $id])
				],
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
	public function delete($id)
	{
		$unit = Unit::find($id);

		// Si existe la unidad...
		if ($unit) {
			// La elimina.
			$unit->delete();

			$responseData = [
				'state' => 'success',
			];
		}
		// Si no...
		else {
			$responseData = [
				'state' => 'error',
				// Crea la unidad.
				'errors' => [
					__('validation.not_exist', ['id' => $id])
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
