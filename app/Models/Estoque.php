<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    protected $fillable = ['produto_id', 'variacao_id', 'quantidade'];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function variacao()
    {
        return $this->belongsTo(Variacao::class);
    }
}
