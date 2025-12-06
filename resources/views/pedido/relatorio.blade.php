<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Pedido - Sistema Cafeteria</title>
<style>
    body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; margin: 1cm 0.5cm; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table th, table td { border: 1px solid black; padding: 5px; text-align: center; }
    .header { text-align: center; margin-bottom: 10px; font-weight: bold; line-height: 1.2; }
    .texto-marca-dagua { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 7em; color: #888; opacity: 0.3; pointer-events: none; }
    img.produto-img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
</style>
</head>
<body>

<div class="texto-marca-dagua">CAFETERIA</div>

<div class="header">
    <h2>RELATÓRIO DE PEDIDO</h2>
</div>

<hr>

<h3>Informações do Cliente</h3>
<table>
    <tr><th>Nome</th><td>{{ $pedido->cliente_nome }}</td></tr>
    <tr><th>Horário</th><td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td></tr>
</table>

<h3 style="margin-top:20px;">Itens do Pedido</h3>
<table>
    <thead>
        <tr>
            <th>Foto</th>
            <th>Produto</th>
            <th>Qtd</th>
            <th>Valor Unitário</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pedido->itens as $item)
            <tr>
                <td>
                    @if($item->produto->foto && file_exists(storage_path('app/public/' . $item->produto->foto)))
                        <img src="{{ public_path('storage/' . $item->produto->foto) }}" class="produto-img" alt="{{ $item->produto->nome }}">
                    @else
                        <span>Sem foto</span>
                    @endif
                </td>
                <td>{{ $item->produto->nome }}</td>
                <td>{{ $item->quantidade }}</td>
                <td>R$ {{ number_format($item->valor, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($item->quantidade * $item->valor, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="4">Total Geral</th>
            <th>R$ {{ number_format($pedido->itens->sum(fn($item) => $item->quantidade * $item->valor), 2, ',', '.') }}</th>
        </tr>
    </tbody>
</table>

</body>
</html>
