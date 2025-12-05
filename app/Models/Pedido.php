<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['cliente_nome', 'finalizado'];

    public function itens()
    {
        return $this->hasMany(PedidoItem::class);
    }
    
}
