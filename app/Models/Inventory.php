<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'deposit_id',
        'quantity'
    ];

    /************************
     * Funciones del modelo *
     ************************/

    /**
     * Relación a productos. Devuelve el prodcuto con el 
     * que este inventario se relaciona.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación a depósitos. Devuelve el prodcuto con el 
     * que este inventario se relaciona.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}
