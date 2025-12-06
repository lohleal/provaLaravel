@extends('templates/main', [
    'titulo' => 'Revisar Pedido',
    'cabecalho' => 'Confirme seu pedido',
    'rota' => '',
    'relatorio' => '',
    'class' => null
])

@section('conteudo')
@if(!session('pedido_id'))
    <div class="alert alert-warning">
        Nenhum pedido em andamento.
    </div>
@else
    @php
        $pedido = \App\Models\Pedido::with('itens.produto')->find(session('pedido_id'));
        $totalPedido = $pedido ? $pedido->itens->sum(fn($item) => $item->quantidade * $item->valor) : 0;
    @endphp

    @if($pedido && $pedido->itens->count() > 0)
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Nome</th>
                    <th>Valor Unitário</th>
                    <th>Quantidade</th>
                    <th>Valor Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->itens as $item)
                    <tr>
                        <td>
                            @if($item->produto->foto && file_exists(storage_path('app/public/' . $item->produto->foto)))
                                <img src="{{ asset('storage/' . $item->produto->foto) }}" alt="{{ $item->produto->nome }}" style="width:50px; height:50px; object-fit:cover; border-radius:6px;">
                            @else
                                <span class="text-muted">Sem foto</span>
                            @endif
                        </td>
                        <td>{{ $item->produto->nome }}</td>
                        <td>R$ {{ number_format($item->valor, 2, ',', '.') }}</td>
                        <td>{{ $item->quantidade }}</td>
                        <td>R$ {{ number_format($item->quantidade * $item->valor, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total:</th>
                    <th>R$ {{ number_format($totalPedido, 2, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="text-center my-4">
            <h5>Escaneie este QR code para o pagamento:</h5>
            <img src="{{ asset('./storage/fotos/qr code tca.jpeg') }}" alt="QR Code" style="width:200px; height:200px;">
        </div>

        <form action="{{ route('pedido.finalizar') }}" method="POST" class="text-center mt-3">
            @csrf
            <button type="submit" class="btn btn-success btn-lg">Finalizar Pedido</button>
        </form>
    @else
        <div class="alert alert-info">
            Nenhum item adicionado ao pedido.
        </div>
    @endif
@endif
@endsection
