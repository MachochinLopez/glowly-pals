<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'description',
        'short_name'
    ];

    /************************
     * Funciones del modelo *
     ************************/

    /**
     * Devuelve el arreglo con la información de la unidad
     * en el formato esperado.
     * 
     * @return array
     */
    public function formatted()
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'short_name' => $this->short_name,
            'created_at' => date('Y-m-d, h:i:s', strtotime($this->created_at)),
            'updated_at' => date('Y-m-d, h:i:s', strtotime($this->updated_at)),
        ];
    }
}
