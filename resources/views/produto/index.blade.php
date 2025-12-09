@extends('templates/main', [
'titulo' => "LOMI COFFEE",
'cabecalho' => 'Cardápio',
'rota' => 'produto.create',
'relatorio' => 'report.produto',
'class' => App\Models\Produto::class,
])

@section('conteudo')

{{-- POPUP SOMENTE PARA CLIENTE SEM NOME DEFINIDO --}}
@if(session('tipo_usuario') === 'cliente' && !session('cliente_nome'))
<div id="clienteModal">
    <div class="modal-content-lomi">
        <h4>Bem-vindo ao LOMI COFFEE</h4>
        <p>Digite seu nome para começar:</p>

        <input type="text" id="clienteNome" class="form-control" placeholder="Seu nome">

        <button class="btn btn-primary mt-3" onclick="salvarCliente()">
            Continuar
        </button>
    </div>
</div>
@endif

{{-- TÍTULO DE BOAS-VINDAS --}}
@if(session('cliente_nome'))
<h5>Bem vindo(a) <strong>{{ session('cliente_nome') }}</strong></h5>
@endif

{{-- CARDÁPIO --}}
<div class="produtos-cards">
    @foreach ($produtos as $item)
    <div class="card-produto">
        {{-- IMAGEM --}}
        @if($item->foto && file_exists(storage_path('app/public/'.$item->foto)))
        <img src="{{ asset('storage/'.$item->foto) }}" class="img-produto-card" alt="{{ $item->nome }}">
        @else
        <div class="sem-foto">Sem foto</div>
        @endif

        {{-- INFORMAÇÕES --}}
        <div class="info-produto">
            <h5>{{ $item->nome }}</h5>
            <p>{{ $item->curso->nome ?? '-' }}</p>
            <p>Porção: {{ $item->porcao }}</p>
            <p>Valor: {{ $item->valor }}</p>

            {{-- CARRINHO --}}
            @if(session('tipo_usuario') === 'cliente')
            <div class="carrinho">
                <button onclick="updateCart({{ $item->id }}, 'decrease')">-</button>
                <span id="qty-{{ $item->id }}">{{ $itensPedido[$item->id] ?? 0 }}</span>
                <button onclick="updateCart({{ $item->id }}, 'increase')">+</button>
            </div>
            @endif

            {{-- AÇÕES --}}
            <div class="acoes">
                <a href="{{ asset('storage/'.$item->foto) }}" target="_blank">Visualizar</a>
                @can('update', $item)
                <a href="{{ route('produto.edit', $item->id) }}">Editar</a>
                @endcan
                @can('delete', $item)
                <a href="#" onclick="showRemoveModal('{{ $item->id }}', '{{ $item->nome }}')">Excluir</a>
                @endcan
            </div>
        </div>
    </div>
    @endforeach
</div>

<a href="{{ route('login') }}" class="btn-voltar-login">Voltar</a>

@if(session('tipo_usuario') === 'cliente')
<a href="{{ route('pedido.revisar') }}" class="btn-finalizar">Finalizar Pedido</a>
@endif

@endsection

<script>
function updateCart(produtoId, action) {
    fetch(`/carrinho/update/${produtoId}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ action: action })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById("qty-" + produtoId).innerText = data.quantidade ?? 0;
        });
}

function salvarCliente() {
    let nome = document.getElementById("clienteNome").value;

    if (nome.trim() === "") {
        alert("Digite seu nome!");
        return;
    }

    fetch("/cliente/store", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ nome: nome })
        })
        .then(res => res.json())
        .then(() => location.reload());
}
</script>

<style>
    /* Fundo da página */
body {
    background-color: #c8d5b9; /* verde musgo pastel clara */
    margin: 0;
    font-family: Arial, sans-serif;
}

/* Modal */
#clienteModal {
    display: block;
    position: fixed;
    top: 0; 
    left: 0; 
    width: 100%; 
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 10000;
}

.modal-content-lomi {
    background: #9ba88e;
    width: 350px;
    margin: 15% auto;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}

/* Botões fixos */
.btn-finalizar, .btn-voltar-login {
    position: fixed;
    bottom: 35px;
    background: #43503E; /* mesmo tom que estava antes */
    color: #fff;
    border: 2px solid #43503E;
    padding: 8px 14px;
    border-radius: 10px;
    font-weight: bold;
    text-decoration: none;
    box-shadow: 0 4px 10px #0003;
    transition: .2s;
    z-index: 1000;
}
.btn-finalizar {
    right: 10px;
}
.btn-voltar-login {
    left: 10px;
}
.btn-finalizar:hover, .btn-voltar-login:hover {
    background: #c8bba7; /* tom ligeiramente mais escuro */
    border-color: #c8bba7;
    transform: scale(1.07);
}

/* Fundo da página / cardápio */
.produtos-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin-top: 20px;
    background-color: #c8d5b9; /* verde musgo pastel clara */
    padding: 20px;
    border-radius: 10px;
}

/* Card do produto */
.card-produto {
    width: 200px;
    background: #e4ebd8; /* tom claro, harmonizando com o fundo */
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    box-shadow: 0 4px 10px #0002;
    transition: transform .2s;
}

.card-produto:hover {
    transform: scale(1.05);
}


.img-produto-card {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 10px;
}

.info-produto h5 {
    margin: 5px 0;
}

.carrinho button {
    margin: 0 5px;
    padding: 4px 8px;
}

.acoes a {
    display: inline-block;
    margin: 5px;
    text-decoration: none;
    color: #43503E;
    font-weight: bold;
}

.sem-foto {
    width: 100%;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ddd;
    border-radius: 10px;
    color: #666;
}
</style>
