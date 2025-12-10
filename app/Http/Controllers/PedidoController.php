<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
 use App\Models\Produto;

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
            $pedido->finalizado = 1;
            $pedido->save();
        }

        session()->forget([
            'pedido_id',
            'cliente_nome',
            'cart'
        ]);

        return redirect('/produto')->with('popup', true);
    }

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

        $qrCodeUrl = asset('storage/qrcode.png');

        return view('pedido.revisar', compact('pedido', 'qrCodeUrl'));
    }

    public function lista()
    {
        $pedidos = Pedido::with('itens.produto')
            ->where('finalizado', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $cabecalho = "Lista de Pedidos";
        $rota = "";
        $relatorio = "";

        return view('pedido.lista', compact('pedidos', 'cabecalho', 'rota', 'relatorio'));
    }

    public function concluir($id)
    {
        $pedido = Pedido::findOrFail($id);

        $pedido->finalizado = 2; 
        $pedido->save();

        return response()->json(['success' => true]);
    }

    public function pdf($id)
    {
        $pedido = Pedido::with('itens.produto')->findOrFail($id);

        $pdf = \PDF::loadView('pedido.relatorio', compact('pedido'));

        return $pdf->stream("pedido_{$id}.pdf");
    }

   


public function relatorioMensal()
{
    $pedidos = Pedido::with('itens.produto')
        ->where('finalizado', 1)
        ->whereMonth('created_at', now()->month)
        ->get();

   
    $produtos = Produto::all()->map(function ($produto) use ($pedidos) {
        $quantidadeVendida = 0;

       
        foreach ($pedidos as $pedido) {
            foreach ($pedido->itens as $item) {
                if ($item->produto_id == $produto->id) {
                  
                    $quantidadeVendida += $item->quantidade;
                }
            }
        }

      
        $produto->quantidade_vendida = $quantidadeVendida;

      
        $produto->valor_total = $produto->valor * $quantidadeVendida;

        return $produto;
    });

   
    $pdf = \PDF::loadView('pedido.relatorio_mensal', compact('produtos'));

    return $pdf->stream('relatorio_mensal.pdf');
}


}
