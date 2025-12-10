    @extends('templates/main', [
        'titulo' => "LOMI COFFEE",
        'cabecalho' => 'Cardápio',
        'rota' => 'produto.create',
        'relatorio' => session('tipo_usuario') === 'cliente' ? null : 'report.produto',
        'class' => App\Models\Produto::class,
    ])

    @section('conteudo')

    {{-- POPUP PARA CLIENTE SEM NOME --}}
    @if(session('tipo_usuario') === 'cliente' && !session('cliente_nome'))
    <div id="clienteModal">
        <div class="popup-content">
            <h4>Bem-vindo ao LOMI COFFEE!!</h4>
            <input type="text" id="clienteNome" class="form-control" placeholder="Digite seu nome">
            <button onclick="salvarCliente()" class="btn-popup">Continuar</button>
        </div>
    </div>
    @endif

    {{-- TÍTULO DE BOAS-VINDAS --}}
    @if(session('cliente_nome'))
    <h5>Bem vindo(a) <strong>{{ session('cliente_nome') }}</strong></h5>
    @endif

    <div class="cardapio-container">
        {{-- BARRA LATERAL --}}
        <aside class="sidebar">
            <h4>Categorias</h4>
            <ul>
                <li><a href="#" onclick="filtrarCategoria('todas'); return false;">TODAS</a></li>
                @foreach($categorias as $categoria)
                    <li>
                        <a href="#" onclick="filtrarCategoria('{{ $categoria->nome }}'); return false;">
                            {{ $categoria->nome }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- ÁREA DE PRODUTOS --}}
        <main class="produtos-cards" id="produtos">
            @foreach ($produtos as $item)
<div class="card-produto" data-categoria="{{ $item->curso->nome ?? 'Sem categoria' }}">
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

        {{-- AÇÕES --}}
        <div class="acoes">
            @can('update', $item)
            <a href="{{ route('produto.edit', $item->id) }}" class="acao-btn editar" title="Editar">
                ✏️
            </a>
            @endcan

            @can('delete', $item)
            <!-- Formulário oculto -->
            <form id="form_{{ $item->id }}" action="{{ route('produto.destroy', $item->id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>

            <a href="#" onclick="showRemoveModal('{{ $item->id }}', '{{ $item->nome }}')" class="acao-btn excluir" title="Excluir">
                ❌
            </a>
            @endcan
        </div>
    </div>
</div>
@endforeach

        </main>
    </div>

    {{-- BOTÕES FIXOS PARA CLIENTE --}}
    @if(session('tipo_usuario') === 'cliente')
    <a href="{{ route('login') }}" class="btn-voltar-login">Voltar</a>
    <a href="{{ route('pedido.revisar') }}" class="btn-finalizar">Finalizar Pedido</a>
    @endif

    {{-- MODAL DE IMAGEM --}}
    <div id="imagemModal">
        <span class="close" onclick="fecharModal()">&times;</span>
        <img id="modalImg" src="" alt="Imagem do produto">
    </div>

    @endsection

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Modal de imagem
        const imagens = document.querySelectorAll('.img-produto-card');
        const modal = document.getElementById('imagemModal');
        const modalImg = document.getElementById('modalImg');
        imagens.forEach(img => {
            img.addEventListener('click', () => {
                modal.style.display = 'flex';
                modalImg.src = img.src;
            });
        });
    });

    function fecharModal() {
        document.getElementById('imagemModal').style.display = 'none';
    }

    function filtrarCategoria(categoria) {
        const cards = document.querySelectorAll('.card-produto');
        cards.forEach(card => {
            if (categoria === 'todas' || card.dataset.categoria === categoria) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.5s ease';
            } else {
                card.style.display = 'none';
            }
        });
    }

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
        if (nome.trim() === "") { alert("Digite seu nome!"); return; }
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
    /* Container principal */
    .cardapio-container {
        display: flex;
        gap: 20px;
    }

    /* Barra lateral */
    .sidebar {
        width: 200px;
        background-color: #43503E;
        color: #fff;
        padding: 20px;
        border-radius: 10px;
        height: fit-content;
    }
    .sidebar h4 {
        margin-bottom: 15px;
    }
    .sidebar ul {
        list-style: none;
        padding: 0;
    }
    .sidebar ul li {
        margin-bottom: 10px;
    }
    .sidebar ul li a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        display: block;
        padding: 6px 10px;
        border-radius: 5px;
        transition: 0.3s;
    }
    .sidebar ul li a:hover {
        background-color: #6d7d6c;
    }

    /* Produtos */
    .produtos-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: flex-start;
        flex: 1;
    }

    .card-produto {
        width: 200px;
        background: #e4ebd8;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        box-shadow: 0 4px 10px #0002;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        0% {opacity: 0; transform: translateY(10px);}
        100% {opacity: 1; transform: translateY(0);}
    }

    .img-produto-card {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .img-produto-card:hover {
        transform: scale(1.05);
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

    .info-produto h5 {
        margin: 5px 0;
    }

    .carrinho {
        margin: 10px 0;
    }
    .btn-carrinho {
        padding: 4px 8px;
        margin: 0 5px;
        border-radius: 5px;
        border: none;
        background-color: #43503E;
        color: #fff;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-carrinho:hover {
        background-color: #6d7d6c;
        transform: scale(1.1);
    }

    /* Ações */
    .acoes {
        margin-top: 5px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    .acao-btn {
        display: inline-block;
        width: 32px;
        height: 32px;
        line-height: 32px;
        border-radius: 50%;
        background-color: #43503E;
        color: #fff;
        font-weight: bold;
        text-align: center;
        cursor: pointer;
        transition: 0.2s;
        vertical-align: middle;
    }

    .acao-btn.editar:hover {
        background-color: #6d7d6c;
    }
    .acao-btn.excluir:hover {
        background-color: #c55c5c;
    }

    /* Botões fixos */
    .btn-finalizar, .btn-voltar-login {
        position: fixed;
        bottom: 35px;
        background: #43503E;
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
    .btn-finalizar { right: 10px; }
    .btn-voltar-login { left: 10px; }
    .btn-finalizar:hover, .btn-voltar-login:hover {
        background: #c8bba7;
        border-color: #c8bba7;
        transform: scale(1.07);
    }

    /* Modal cliente */
    #clienteModal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.55);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    #clienteModal .popup-content {
        background: #f7e9d8;
        border: 3px solid #43503d;
        border-radius: 20px;
        padding: 40px;
        width: 400px;
        text-align: center;
        box-shadow: 0 6px 25px #0006;
        color: #4b2e1e;
        font-family: Georgia, serif;
    }
    #clienteModal .form-control {
        display: block;
        width: 90%;
        padding: 10px 16px;
        border-radius: 25px;
        border: 2px solid #43503d;
        font-size: 16px;
        outline: none;
        transition: 0.3s;
        text-align: center;
        margin: 20px auto;
    }
    #clienteModal .form-control:focus {
        border-color: #c8bba7;
        box-shadow: 0 0 5px #c8bba7;
    }
    .btn-popup {
        display: inline-block;
        margin: 0 auto;
        background: #d8cbb7;
        color: #43503d;
        border: 2px solid #43503d;
        padding: 10px 22px;
        border-radius: 15px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-popup:hover {
        background: #c8bba7;
        transform: scale(1.07);
    }

    /* Modal de imagem */
    #imagemModal {
        display: none;
        position: fixed;
        z-index: 9999;
        padding-top: 60px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.75);
        align-items: center;
        justify-content: center;
    }
    #imagemModal img {
        max-width: 80%;
        max-height: 80%;
        border-radius: 10px;
    }
    #imagemModal .close {
        position: absolute;
        top: 20px;
        right: 35px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }
    </style>
