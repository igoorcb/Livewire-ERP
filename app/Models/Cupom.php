<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    protected $fillable = ['codigo', 'validade', 'valor_minimo', 'desconto', 'tipo'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
