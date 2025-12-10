@extends('templates.main', ['titulo' => 'Pedidos', 'rota' => '', 'relatorio' => ''])

@section('titulo') Pedidos Realizados @endsection

@section('conteudo')
<table class="table table-striped table-ordered table-hover" id="pedidosTable">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Horário</th>
            <th>Total</th>
            <th>Ações</th>
        </tr>
    </thead>

    <tbody>
        @foreach($pedidos as $p)
        @php
        $totalPedido = $p->itens->sum(fn($item) => $item->quantidade * $item->valor);
        @endphp
        <tr>
            <td>{{ $p->cliente_nome }}</td>
            <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
            <td>R$ {{ number_format($totalPedido, 2, ',', '.') }}</td>
            <td>
                <a href="{{ route('pedido.pdf', $p->id) }}" class="btn btn-success btn-sm" target="_blank">
                    📄 Comanda
                </a>

                <button type="button" class="btn btn-success btn-sm ms-1 concluir-btn" data-id="{{ $p->id }}">
                    ✅
                </button>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.concluir-btn');

    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const pedidoId = this.dataset.id;
            const row = this.closest('tr');

            fetch(`/pedido/concluir/${pedidoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                } else {
                    alert('Erro ao concluir o pedido');
                }
            });
        });
    });
});
</script>

@endsection