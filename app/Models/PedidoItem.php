<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    use HasFactory;

    protected $table = 'pedido_itens'; // <- aqui você força a tabela correta

    protected $fillable = ['pedido_id', 'produto_id', 'quantidade', 'valor'];
    // App/Models/PedidoItem.php
    public function produto()
    {
        return $this->belongsTo(Aluno::class, 'produto_id');
    }
}



