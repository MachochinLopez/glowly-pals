<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'description',
        'unit_id'
    ];

    /************************
     * Funciones del modelo *
     ************************/

    /**
     * Relación a inventarios. Devuelve los inventarios con los 
     * que este producto se relaciona.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
