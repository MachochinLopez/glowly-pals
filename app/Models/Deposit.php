<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'user_id',
        'description'
    ];

    /************************
     * Funciones del modelo *
     ************************/

    /**
     * Relación a usuarios. Devuelve el usuario con el 
     * que este inventario se relaciona.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /************************
     * Funciones del modelo *
     ************************/

    /**
     * Devuelve el arreglo con la información del inventario
     * en el formato esperado.
     * 
     * @return array
     */
    public function formatted()
    {
        return [
            'deposit_id' => $this->id,
            'deposit_description' => $this->description,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'created_at' => date('Y-m-d, h:i:s', strtotime($this->created_at)),
            'updated_at' => date('Y-m-d, h:i:s', strtotime($this->updated_at)),
        ];
    }
}
