<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produto;

class PedidoItem extends Model
{
    protected $table = 'pedido_itens';

    protected $fillable = ['pedido_id', 'produto_id', 'quantidade', 'valor'];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
