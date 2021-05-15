<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Inventory;

class InventoryController extends Controller
{
    protected $modelName = 'Inventario';

    /**
     * Devuelve la lista de todas los Inventarios.
     * 
     * @return json
     */
    public function index()
    {
        // Listamos los inventarios agrupados por producto.
        $inventories = Inventory::all();
        return response()->json([
            'data' => $inventories,
        ]);
    }

    /**
     * Muestra el detalle individual de un Inventario.
     * 
     * @param int $id Product Id
     * @return json
     */
    public function show($id)
    {
        $inventory = Inventory::find($id);

        // Si la unidad existe...
        if ($inventory) {
            $responseData = [
                'state' => 'success',
                'data' => $inventory,
            ];
        }
        // Si no...
        else {
            $responseData = [
                'state' => 'error',
                'errors' => [
                    __('validation.exists', ['modelName' => $this->modelName])
                ],
            ];
        }

        return response()->json($responseData);
    }

    /**
     * Crea una nueva Inventario.
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
                'message' => __('validation.success_messages.masculine.create', ['modelName' => $this->modelName]),
                // Crea la Inventario.
                'data' => Inventory::create(request()->all()),
            ];
        }
        // Si no...
        else {
            $responseData = [
                'state' => $validatedData['state'],
                // Crea la Inventario.
                'errors' => $validatedData['errors'],
            ];
        }

        return response()->json($responseData);
    }

    /**
     * Actualiza una Inventario existente.
     * 
     * @param int $id Product Id
     * @return json
     */
    public function update($id)
    {
        $inventory = Inventory::find($id);

        // Si la Inventario existe...
        if ($inventory) {
            $validatedData = $this->validateRequest();

            // Si pasa la validación...
            if ($validatedData['state'] == 'success') {
                // Actualiza el registro.
                $inventory->update(request()->all());

                $responseData = [
                    'state' => $validatedData['state'],
                    'message' => __('validation.success_messages.masculine.edit', ['modelName' => $this->modelName]),
                    // Crea la Inventario.
                    'data' => $inventory,
                ];
            }
            // Si no...
            else {
                $responseData = [
                    'state' => $validatedData['state'],
                    // Crea la Inventario.
                    'errors' => $validatedData['errors'],
                ];
            }
        }
        // Si no...
        else {
            $responseData = [
                'state' => 'error',
                // Crea la Inventario.
                'errors' => [
                    __('validation.exists', ['modelName' => 'Inventario'])
                ],
            ];
        }

        return response()->json($responseData);
    }

    /**
     * Actualiza una Inventario existente.
     * 
     * @param int $id Product Id
     * @return json
     */
    public function delete($id)
    {
        $inventory = Inventory::find($id);

        // Si existe la Inventario...
        if ($inventory) {
            // La elimina.
            $inventory->delete();

            $responseData = [
                'state' => 'success',
                'message' => __('validation.success_messages.masculine.delete', ['modelName' => $this->modelName]),
            ];
        }
        // Si no...
        else {
            $responseData = [
                'state' => 'error',
                // Crea la Inventario.
                'errors' => [
                    __('validation.exists', ['modelName' => 'Inventario'])
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

        $messages = [
            'required' => __('validation.required'),
            'exists' => __('validation.exists', ['modelName' => 'Unidad'])
        ];

        // Validador del request.
        $validator = Validator::make(request()->all(), $rules, $messages);
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
