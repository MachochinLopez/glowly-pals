<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Inventory;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{
	protected $modelName = 'Producto';

	/**
	 * Devuelve la lista de todos los productos.
	 * 
	 * @return json
	 */
	public function index()
	{
		// Toma todos los productos.
		$products = Product::all()->map(function ($product) {
			// Y devuelve cada uno con el formato esperado.
			return $product->formatted();
		});

		return response()->json([
			'data' => $products,
		]);
	}

	/**
	 * Muestra el detalle individual de un producto.
	 * 
	 * @param int $id Product Id
	 * @return json
	 */
	public function show($id)
	{
		$product = Product::find($id);

		// Si el producto existe...
		if ($product) {
			// Forma el contenido de la respuesta.
			$responseData = [
				'state' => 'success',
				'data' => $product->formatted(),
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
	 * Crea un nuevo producto.
	 * 
	 * @return json
	 */
	public function store()
	{
		$validatedData = $this->validateRequest();

		// Si pasa la validación...
		if ($validatedData['state'] == 'success') {
			// Crea el producto.
			$product = Product::create(request()->all())->formatted();
			$deposits = Deposit::all();

			// Por cada depósito.
			foreach ($deposits as $deposit) {
				// Crea un inventario.
				Inventory::create([
					'product_id' => $product["product_id"],
					'deposit_id' => $deposit->id,
					'quantity' => 0
				]);
			}

			// Forma la respuesta.
			$responseData = [
				'state' => $validatedData['state'],
				'message' => __(
					'validation.success_messages.masculine.create',
					['modelName' => $this->modelName]
				),
				// Crea el producto.
				'data' => $product,
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
	 * Actualiza un producto existente.
	 * 
	 * @param int $id Product Id
	 * @return json
	 */
	public function update($id)
	{
		$product = Product::find($id);

		// Si el producto existe...
		if ($product) {
			$validatedData = $this->validateRequest();

			// Si pasa la validación...
			if ($validatedData['state'] == 'success') {
				// Actualiza el registro.
				$product->update(request()->all());

				$responseData = [
					'state' => $validatedData['state'],
					'message' => __(
						'validation.success_messages.masculine.edit',
						['modelName' => $this->modelName]
					),
					// Crea el producto.
					'data' => $product->formatted(),
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
	 * Elimina un producto.
	 * 
	 * @param int $id Product Id
	 * @return json
	 */
	public function delete($id)
	{
		$product = Product::find($id);

		// Si existe el producto...
		if ($product) {
			// La elimina.
			$product->delete();

			$responseData = [
				'state' => 'success',
				'message' => __(
					'validation.success_messages.masculine.delete',
					['modelName' => $this->modelName]
				),
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
			'unit_id' => 'required|exists:units,id',
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
