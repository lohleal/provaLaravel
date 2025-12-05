@extends('templates/main', [
    'titulo' => "LOMI COFFEE",
    'cabecalho' => 'Lista de Produtos',
    'rota' => 'aluno.create',
    'relatorio' => 'report.aluno',
    'class' => App\Models\Aluno::class,
])

@section('conteudo')
<table class="table align-middle caption-top table-striped">
    <thead>
        <th class="text-secondary">PRODUTO</th>
        <th class="text-secondary">NOME</th>
        <th class="d-none d-md-table-cell text-secondary">CATEGORIA</th>
        <th class="d-none d-md-table-cell text-secondary">PORÇÕES</th>
        <th class="d-none d-md-table-cell text-secondary">VALOR</th>
        <th class="text-center text-secondary">CARRINHO</th>
        <th class="text-center text-secondary">AÇÕES</th>
    </thead>
    <tbody>


    @if(session('popup'))
        <script>
            window.onload = function() {
                document.getElementById('popupCliente').style.display = 'block';
            }
        </script>
    @endif

    @if(session('cliente_nome'))
        <h5>Bem vindo(a) <strong>{{ session('cliente_nome') }}</strong></h5>
    @endif

            @if(!session('cliente_nome'))
        <div id="clienteModal" style="
            display:block;
            position:fixed;
            top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.6);
            z-index:10000;
        ">
            <div style="
                background:#9ba88e;
                width:350px;
                margin:15% auto;
                padding:20px;
                border-radius:10px;
                text-align:center;
            ">
                <h4>Bem-vindo ao LOMI COFFEE</h4>
                <p>Digite seu nome para começar:</p>

                <input type="text" id="clienteNome" class="form-control" placeholder="Seu nome">

                <button class="btn btn-primary mt-3" onclick="salvarCliente()">
                    Continuar
                </button>
            </div>
        </div>
        @endif

        @foreach ($alunos as $item)
        <tr>
            {{-- PRODUTO (IMAGEM) --}}
            <td>
                @if($item->foto && file_exists(storage_path('app/public/'.$item->foto)))
                    <img src="{{ asset('storage/'.$item->foto) }}"
                         alt="{{ $item->nome }}"
                         style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                @else
                    <span class="text-muted">Sem foto</span>
                @endif
            </td>

            {{-- NOME --}}
            <td>{{ $item->nome }}</td>

            {{-- CATEGORIA --}}
            <td class="d-none d-md-table-cell">{{ $item->curso->nome ?? '-' }}</td>

            {{-- PORÇÕES --}}
            <td class="d-none d-md-table-cell">{{ $item->porcao }}</td>

            {{-- VALOR --}}
            <td class="d-none d-md-table-cell">{{ $item->valor }}</td>

            {{-- CARRINHO --}}
            <td class="text-center">
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <button onclick="updateCart({{ $item->id }}, 'decrease')" class="btn btn-outline-danger btn-sm">-</button>
                    <span id="qty-{{ $item->id }}">{{ $itensPedido[$item->id] ?? 0 }}</span>
                    <button onclick="updateCart({{ $item->id }}, 'increase')" class="btn btn-outline-success btn-sm">+</button>
                </div>
            </td>

            {{-- AÇÕES --}}
            <td class="text-center">
                {{-- Visualizar --}}
                <a href="{{ asset('storage/'.$item->foto) }}" target="_blank" class="btn btn-outline-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         fill="#000" class="bi bi-person-bounding-box" viewBox="0 0 16 16">
                        <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5"/>
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                    </svg>
                </a>

                {{-- Editar --}}
                @can('update', $item)
                    <a href="{{ route('aluno.edit', $item->id) }}" class="btn btn-outline-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#5cb85c" class="bi bi-arrow-counterclockwise" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
                            <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
                        </svg>
                    </a>
                @endcan

                {{-- Excluir --}}
                @can('delete', $item)
                    <a href="#" style="cursor:pointer" onclick="showRemoveModal('{{ $item->id }}', '{{ $item->nome }} - {{ $item->curso->nome ?? '' }}')" class="btn btn-outline-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#d9534f" class="bi bi-trash-fill" viewBox="0 0 16 16">
                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                        </svg>
                    </a>

                    <form id="form_{{$item->id}}" action="{{ route('aluno.destroy', $item->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                    </form>
                @endcan
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
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
    })
    .catch(err => console.error(err));
}

</script>


<script>
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
<a href="{{ route('pedido.revisar') }}" class="btn-finalizar">Finalizar Pedido</a>


<style>
    #finalizarPedidoBtn {
        position: fixed;
        bottom: 35px;
        right: 10px;
        z-index: 9999;
    }

    .btn-finalizar {
        background: #d8cbb7;
        color: #43503d;
        border: 2px solid #43503d;
        font-size: .9rem;
        padding: 8px 14px;
        border-radius: 10px;
        font-weight: bold;
        box-shadow: 0 4px 10px #0003;
        transition: .2s;
    }

    .btn-finalizar:hover {
        background: #c8bba7;
        transform: scale(1.07);
    }
</style>