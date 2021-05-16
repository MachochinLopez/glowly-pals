<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Inventory;
use App\Models\Product;

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
        $inventories = Inventory::select(
            'inventories.id AS inventory_id',
            'inventories.product_id AS product_id',
            'products.description AS product_description',
            DB::raw('SUM(inventories.quantity) AS quantity')
        )
            ->leftJoin('products', 'inventories.product_id', 'products.id')
            ->groupBy('product_id')
            ->get();

        return response()->json([
            'data' => $inventories,
        ]);
    }

    /**
     * Muestra el detalle individual de un Inventario.
     * 
     * @param int $productId Product Id
     * @return json
     */
    public function show($productId)
    {
        $product = Product::find($productId);

        // Si el producto existe...
        if ($product) {
            // Variabla que contendrá los datos de los
            // inventarios.
            $responseData = [];
            // Toma los inventarios de ese producto.
            $inventories = $product->inventories;

            // Por cada inventario...
            foreach ($inventories as $inventory) {
                $formattedInventory = [
                    'inventory_id' => $inventory->id,
                    'deposit_id' => $inventory->deposit->id,
                    'deposit_description' => $inventory->deposit->description,
                    'quantity' => $inventory->quantity
                ];

                $inventoryId = $formattedInventory['inventory_id'];
                // Agrega el depósito al arreglo de depósitos.
                $responseData['deposits'][$inventoryId] = $formattedInventory;
            }

            // Agrega la información del producto.
            $responseData['product_id'] = $product->id;
            $responseData['product_description'] = $product->description;

            $responseData = [
                'state' => 'success',
                'data' => $responseData,
            ];
        }
        // Si no...
        else {
            $responseData = [
                'state' => 'error',
                'errors' => [
                    __(
                        'validation.exists',
                        ['attribute' => 'Producto']
                    )
                ],
            ];
        }

        return response()->json($responseData);
    }

    /**
     * Maneja las entradas de Inventario.
     * 
     * @return json
     */
    public function handleInventoryEntry()
    {
        $validatedData = $this->validateRequest();

        // Si pasa la validación...
        if ($validatedData['state'] == 'success') {

            $inventory = Inventory::where([
                ['deposit_id', request()->deposit_id],
                ['product_id', request()->product_id],
            ])->first();

            // Y no se ha creado un inventario para ese mismo depósito
            // ni ese mismo producto...
            if (!$inventory) {
                // Lo crea.
                $inventory = Inventory::create(request()->all());
            }
            // Si sí...
            else {
                // Toma la cantidad anterior de inventario.
                $previousAmount = $inventory->quantity;
                // La suma.
                $newAmount = $previousAmount + request()->quantity;
                // Y lo actualiza.
                $inventory->update([
                    'quantity' => $newAmount
                ]);
            }

            // Forma la respuesta.
            $responseData = [
                'state' => $validatedData['state'],
                'message' => __('validation.entry'),
                'data' => $inventory,
            ];
        }
        // Si no...
        else {
            // Forma la respuesta.
            $responseData = [
                'state' => $validatedData['state'],
                // Devuelve los errores de validación..
                'errors' => $validatedData['errors'],
            ];
        }

        return response()->json($responseData);
    }

    /**
     * Maneja las salidas de Inventario.
     * 
     * @return json
     */
    public function handleInventoryExit()
    {
        $validatedData = $this->validateRequest();

        // Si pasa la validación...
        if ($validatedData['state'] == 'success') {

            $inventory = Inventory::where([
                ['deposit_id', request()->deposit_id],
                ['product_id', request()->product_id],
            ])->first();

            // Toma la cantidad anterior de inventario.
            $previousAmount = $inventory->quantity;
            // La resta.
            $newAmount = $previousAmount - request()->quantity;

            // Si la nueva cantidad de producto es menor a 0.
            if ($newAmount < 0) {
                // Marca el error.
                $responseData = [
                    'state' => 'error',
                    'errors' => [
                        __(
                            'validation.min_amount_error',
                            ['current_amount' => $previousAmount]
                        )
                    ],
                ];
            }
            // Si no...
            else {
                // Y lo actualiza.
                $inventory->update([
                    'quantity' => $newAmount
                ]);

                // Forma la respuesta exitosa.
                $responseData = [
                    'state' => $validatedData['state'],
                    'message' => __('validation.exit'),
                    'data' => $inventory,
                ];
            }
        }
        // Si no...
        else {
            // Forma la respuesta.
            $responseData = [
                'state' => $validatedData['state'],
                // Devuelve los errores de validación..
                'errors' => $validatedData['errors'],
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
            'quantity' => 'required|integer|min:1',
            'deposit_id' => 'required|exists:deposits,id',
            'product_id' => 'required|exists:products,id',
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
