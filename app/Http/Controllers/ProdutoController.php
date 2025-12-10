<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Curso; // Em breve será Categoria
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Produto::class);

        $produtos = Produto::with('curso')
                           ->orderBy('curso_id')
                           ->orderBy('nome')
                           ->get();

        // BUSCA ITENS DO PEDIDO ATUAL
        $pedidoId = session('pedido_id');

        $itensPedido = [];
        if ($pedidoId) {
            $itensPedido = \App\Models\PedidoItem::where('pedido_id', $pedidoId)
                                                 ->pluck('quantidade', 'produto_id')
                                                 ->toArray();
        }

        return view('produto.index', compact('produtos', 'itensPedido'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Produto::class);

        // Isso futuramente será Categoria::all()
        $cursos = Curso::orderBy('duracao')->get();

        return view('produto.create', compact('cursos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Produto::class);

        $curso = Curso::find($request->curso);

        if ($curso) {
            $produto = new Produto();
            $produto->nome = mb_strtoupper($request->nome, 'UTF-8');
            $produto->porcao = $request->porcao;
            $produto->valor = floatval(str_replace(',', '.', $request->valor));
            $produto->curso()->associate($curso);
            $produto->save();

            // Upload de foto
            if ($request->hasFile('foto')) {

                $file = $request->file('foto');
                $name = $produto->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('fotos', $name, 'public');

                $produto->foto = 'fotos/' . $name;
                $produto->save();
            }
        }

        return redirect()->route('produto.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $produto = Produto::find($id);
        Gate::authorize('update', $produto);

        if ($produto) {
            $cursos = Curso::orderBy('duracao')->get();
            return view('produto.edit', compact('produto', 'cursos'));
        }

        return redirect()->route('produto.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produto = Produto::find($id);
        Gate::authorize('update', $produto);

        $curso = Curso::find($request->curso);

        if ($produto && $curso) {

            $produto->nome = mb_strtoupper($request->nome, 'UTF-8');
            $produto->porcao = $request->porcao;
            $produto->valor = floatval(str_replace(',', '.', $request->valor));
            $produto->curso()->associate($curso);

            // Substituir foto
            if ($request->hasFile('foto')) {

                // Excluir foto antiga
                if ($produto->foto && Storage::disk('public')->exists($produto->foto)) {
                    Storage::disk('public')->delete($produto->foto);
                }

                $file = $request->file('foto');
                $name = $produto->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('fotos', $name, 'public');
                $produto->foto = 'fotos/' . $name;
            }

            $produto->save();
        }

        return redirect()->route('produto.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produto = Produto::find($id);
        Gate::authorize('delete', $produto);

        if ($produto) {

            // Remover foto antes de deletar o registro
            if ($produto->foto && Storage::disk('public')->exists($produto->foto)) {
                Storage::disk('public')->delete($produto->foto);
            }

            $produto->delete();
        }

        return redirect()->route('produto.index');
    }

    /**
     * Generate PDF report.
     */
   public function report()
    {
        // Carrega produtos junto com curso
        $produtos = Produto::with('curso')->get();

        // Calcula quantidade vendida e valor total de cada item
        foreach ($produtos as $produto) {
            
            // BUSCA TODAS AS VENDAS DO PRODUTO
            $quantidadeVendida = \App\Models\PedidoItem::where('produto_id', $produto->id)
                                ->sum('quantidade');

            // CALCULA TOTAL
            $valorTotal = $quantidadeVendida * $produto->valor;

            // Adiciona ao objeto (não salva no banco)
            $produto->quantidade_vendida = $quantidadeVendida;
            $produto->valor_total = $valorTotal;
        }

        // Gera PDF
        $pdf = \PDF::loadView('produto.report', compact('produtos'));

        return $pdf->stream('produtos.pdf');
    }
}
