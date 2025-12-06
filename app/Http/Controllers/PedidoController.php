<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;

class PedidoController extends Controller
{
    public function finalizar(Request $request)
{
    $pedidoId = session('pedido_id');

    if (!$pedidoId) {
        return redirect('/home')->with('popup', true);
    }

    $pedido = Pedido::find($pedidoId);

    if ($pedido) {
        $pedido->finalizado = true;
        $pedido->save();
    }

    // Limpa tudo da sessão
    session()->forget([
        'pedido_id',
        'cliente_nome',
        'cart'
    ]);

    // Volta ao início e abre popup de nome
    return redirect('/aluno')->with('popup', true);
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

public function lista()
{
    $pedidos = \App\Models\Pedido::with('itens.produto')
        ->orderBy('created_at', 'desc')
        ->get();

    $cabecalho = "Lista de Pedidos";
    $rota = "";
    $relatorio = "";

    return view('pedido.lista', compact('pedidos', 'cabecalho', 'rota', 'relatorio'));
}

public function pdf($id)
{
    $pedido = \App\Models\Pedido::with('itens.produto')->findOrFail($id);

    $pdf = \PDF::loadView('pedido.relatorio', compact('pedido'));

    // NÃO BAIXA — APENAS VISUALIZA NO NAVEGADOR
    return $pdf->stream("pedido_{$id}.pdf");
}



}
