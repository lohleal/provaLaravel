<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;

class PedidoController extends Controller
{
    // ============================
    // CLIENTE FINALIZA O PEDIDO
    // ============================
    public function finalizar(Request $request)
    {
        $pedidoId = session('pedido_id');

        if (!$pedidoId) {
            return redirect('/home')->with('popup', true);
        }

        $pedido = Pedido::find($pedidoId);

        if ($pedido) {
            $pedido->finalizado = 1; // CLIENTE FINALIZOU O PEDIDO
            $pedido->save();
        }

        // Limpa tudo da sessão
        session()->forget([
            'pedido_id',
            'cliente_nome',
            'cart'
        ]);

        // Volta ao início e abre popup de nome para novo cliente
        return redirect('/aluno')->with('popup', true);
    }


    // ============================
    // CLIENTE REVISAR PEDIDO
    // ============================
    public function revisar()
    {
        $pedidoId = session('pedido_id');

        if (!$pedidoId) {
            return redirect()->route('home')->with('error', 'Nenhum pedido encontrado.');
        }

        $pedido = Pedido::with('itens.produto')->find($pedidoId);

        if (!$pedido) {
            return redirect()->route('home')->with('error', 'Pedido não encontrado.');
        }

        // Exemplo de QR Code (caso tenha)
        $qrCodeUrl = asset('storage/qrcode.png');

        return view('pedido.revisar', compact('pedido', 'qrCodeUrl'));
    }


    // ============================
    // FUNCIONÁRIO — LISTA PEDIDOS
    // ============================
    public function lista()
    {
        // SOMENTE pedidos que o cliente finalizou (1)
        $pedidos = Pedido::with('itens.produto')
            ->where('finalizado', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $cabecalho = "Lista de Pedidos";
        $rota = "";
        $relatorio = "";

        return view('pedido.lista', compact('pedidos', 'cabecalho', 'rota', 'relatorio'));
    }


    // ============================
    // FUNCIONÁRIO CONCLUI O PEDIDO
    // ============================
    public function concluir($id)
    {
        $pedido = Pedido::findOrFail($id);

        $pedido->finalizado = 2; // FUNCIONÁRIO ATENDEU
        $pedido->save();

        return response()->json(['success' => true]);
    }


    // ============================
    // GERAR PDF DA COMANDA
    // ============================
    public function pdf($id)
    {
        $pedido = Pedido::with('itens.produto')->findOrFail($id);

        $pdf = \PDF::loadView('pedido.relatorio', compact('pedido'));

        return $pdf->stream("pedido_{$id}.pdf");
    }
}
