<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
 use App\Models\Produto;

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
        return redirect('/produto')->with('popup', true);
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

   


public function relatorioMensal()
{
    // Pega todos os pedidos finalizados do mês atual
    $pedidos = Pedido::with('itens.produto')
        ->where('finalizado', 1)
        ->whereMonth('created_at', now()->month)
        ->get();

    // Pega todos os produtos
    $produtos = Produto::all()->map(function ($produto) use ($pedidos) {
        $quantidadeVendida = 0;

        // Percorre todos os pedidos
        foreach ($pedidos as $pedido) {
            foreach ($pedido->itens as $item) {
                if ($item->produto_id == $produto->id) {
                    // Soma a quantidade vendida
                    $quantidadeVendida += $item->quantidade;
                }
            }
        }

        // Adiciona a quantidade vendida ao objeto do produto
        $produto->quantidade_vendida = $quantidadeVendida;

        // Calcula o valor total vendido
        $produto->valor_total = $produto->valor * $quantidadeVendida;

        return $produto;
    });

    // Gera o PDF
    $pdf = \PDF::loadView('pedido.relatorio_mensal', compact('produtos'));

    return $pdf->stream('relatorio_mensal.pdf');
}


}
