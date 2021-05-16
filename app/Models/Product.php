<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'description',
        'unit_id'
    ];

    /**************
     * Relaciones *
     **************/

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

    /**
     * Relación a unidades. Devuelve la unidad con la
     * que este producto se relaciona.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /************************
     * Funciones del modelo *
     ************************/

    /**
     * Devuelve el arreglo con la información del producto
     * en el formato esperado.
     * 
     * @return array
     */
    public function formatted()
    {
        return [
            'unit_id' => $this->unit->id,
            'unit_description' => $this->unit->description,
            'unit_short_name' => $this->unit->short_name,
            'product_id' => $this->id,
            'product_description' => $this->description,
            'created_at' => date('Y-m-d, h:i:s', strtotime($this->created_at)),
            'updated_at' => date('Y-m-d, h:i:s', strtotime($this->updated_at)),
        ];
    }
}
