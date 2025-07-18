<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $fillable = ['nome', 'preco'];

    public function variacoes()
    {
        return $this->hasMany(Variacao::class);
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class);
    }
}
