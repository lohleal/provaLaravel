<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;

class CarrinhoController extends Controller
{
    public function update($id, Request $request)
    {
        $pedidoId = session('pedido_id');

        if (!$pedidoId) {
            return response()->json(['quantidade' => 0, 'error' => 'Pedido não encontrado'], 400);
        }

        $pedido = Pedido::find($pedidoId);
        if (!$pedido) {
            return response()->json(['quantidade' => 0, 'error' => 'Pedido não encontrado'], 400);
        }

        $produto = Produto::find($id);
        if (!$produto) {
            return response()->json(['quantidade' => 0, 'error' => 'Produto não encontrado'], 400);
        }

        $item = PedidoItem::firstOrCreate(
            ['pedido_id' => $pedido->id, 'produto_id' => $id],
            ['quantidade' => 0, 'valor' => $produto->valor]
        );

        if ($request->action === 'increase') {
            $item->quantidade++;
        }

        if ($request->action === 'decrease' && $item->quantidade > 0) {
            $item->quantidade--;
        }

        $item->save();

        return response()->json([
            'quantidade' => $item->quantidade
        ]);
    }
}
