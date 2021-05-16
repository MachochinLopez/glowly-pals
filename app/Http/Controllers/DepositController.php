<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Deposit;

class DepositController extends Controller
{
    protected $modelName = 'Depósito';

    /**
     * Devuelve la lista de todos los depósitos.
     * 
     * @return json
     */
    public function index()
    {
        $deposits = Deposit::all();
        return response()->json([
            'data' => $deposits,
        ]);
    }

    /**
     * Muestra el detalle individual de un depósito.
     * 
     * @param int $id Product Id
     * @return json
     */
    public function show($id)
    {
        $deposit = Deposit::find($id);

        // Si el depósito existe...
        if ($deposit) {
            // Da formato al depósito.
            $formattedProduct = [
                'unit_id' => $deposit->unit->id,
                'unit_description' => $deposit->unit->description,
                'unit_short_name' => $deposit->unit->short_name,
                'product_id' => $deposit->id,
                'product_description' => $deposit->description,
            ];

            $responseData = [
                'state' => 'success',
                'data' => $formattedProduct,
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
     * Crea un nuevo depósito.
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
                    'validation.success_messages.masculine.create',
                    ['modelName' => $this->modelName]
                ),
                // Crea la depósito.
                'data' => Deposit::create(request()->all()),
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
     * Actualiza un depósito existente.
     * 
     * @param int $id Product Id
     * @return json
     */
    public function update($id)
    {
        $deposit = Deposit::find($id);

        // Si la depósito existe...
        if ($deposit) {
            $validatedData = $this->validateRequest();

            // Si pasa la validación...
            if ($validatedData['state'] == 'success') {
                // Actualiza el registro.
                $deposit->update(request()->all());

                $responseData = [
                    'state' => $validatedData['state'],
                    'message' => __(
                        'validation.success_messages.masculine.edit',
                        ['modelName' => $this->modelName]
                    ),
                    // Crea la depósito.
                    'data' => $deposit,
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
     * Elimina un depósito.
     * 
     * @param int $id Product Id
     * @return json
     */
    public function delete($id)
    {
        $deposit = Deposit::find($id);

        // Si existe la depósito...
        if ($deposit) {
            // La elimina.
            $deposit->delete();

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
