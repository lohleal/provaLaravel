<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;

class ClienteController extends Controller
{
    public function store(Request $request)
    {
        $pedido = Pedido::create([
            'cliente_nome' => $request->nome,
            'finalizado' => false,
        ]);

        session([
            'cliente_nome' => $request->nome,
            'pedido_id' => $pedido->id
        ]);

        return response()->json(['ok' => true]);
    }
}
