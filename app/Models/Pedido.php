<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'cliente', 'email', 'cep', 'endereco', 'status',
        'subtotal', 'frete', 'total', 'cupom_id'
    ];

    public function itens()
    {
        return $this->hasMany(ItemPedido::class);
    }

    public function cupom()
    {
        return $this->belongsTo(Cupom::class);
    }
}
