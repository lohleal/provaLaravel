<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;

class PedidoController extends Controller
{
    public function finalizar(Request $request)
    {
        $pedido = Pedido::find(session('pedido_id'));

        if ($pedido) {
            $pedido->finalizado = true;
            $pedido->save();
        }

        session()->forget(['cliente_nome', 'pedido_id']);

        return redirect('/home')->with('popup', true);
    }

    public function revisar()
{
    $pedidoId = session('pedido_id');

    if (!$pedidoId) {
        return redirect()->route('home')->with('error', 'Nenhum pedido encontrado.');
    }

    $pedido = \App\Models\Pedido::with('itens.produto')->find($pedidoId);

    if (!$pedido) {
        return redirect()->route('home')->with('error', 'Pedido não encontrado.');
    }

    // Aqui você pode colocar o caminho do QR Code
    $qrCodeUrl = asset('storage/qrcode.png'); // substitua pelo caminho real

    return view('pedido.revisar', compact('pedido', 'qrCodeUrl'));
}

}
