<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aluno;
use App\Models\Curso; // Em breve será Categoria
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Aluno::class);

        // Lista produtos ordenados pela categoria e pelo nome
        $alunos = Aluno::with('curso')
                       ->orderBy('curso_id')
                       ->orderBy('nome')
                       ->get();

        return view('aluno.index', compact('alunos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Aluno::class);

        // Isso futuramente será Categoria::all()
        $cursos = Curso::orderBy('duracao')->get();

        return view('aluno.create', compact('cursos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Aluno::class);

        $curso = Curso::find($request->curso);

        if ($curso) {
            $aluno = new Aluno();
            $aluno->nome = mb_strtoupper($request->nome, 'UTF-8');
            $aluno->porcao = $request->porcao;
            $aluno->valor = floatval(str_replace(',', '.', $request->valor));
            $aluno->curso()->associate($curso);
            $aluno->save();

            // Upload de foto
            if ($request->hasFile('foto')) {

                $file = $request->file('foto');
                $name = $aluno->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('fotos', $name, 'public');

                $aluno->foto = 'fotos/' . $name;
                $aluno->save();
            }
        }

        return redirect()->route('aluno.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $aluno = Aluno::find($id);
        Gate::authorize('update', $aluno);

        if ($aluno) {
            $cursos = Curso::orderBy('duracao')->get();
            return view('aluno.edit', compact('aluno', 'cursos'));
        }

        return redirect()->route('aluno.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $aluno = Aluno::find($id);
        Gate::authorize('update', $aluno);

        $curso = Curso::find($request->curso);

        if ($aluno && $curso) {

            $aluno->nome = mb_strtoupper($request->nome, 'UTF-8');
            $aluno->porcao = $request->porcao;
            $aluno->valor = floatval(str_replace(',', '.', $request->valor));
            $aluno->curso()->associate($curso);

            // Substituir foto
            if ($request->hasFile('foto')) {

                // Excluir foto antiga
                if ($aluno->foto && Storage::disk('public')->exists($aluno->foto)) {
                    Storage::disk('public')->delete($aluno->foto);
                }

                $file = $request->file('foto');
                $name = $aluno->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('fotos', $name, 'public');
                $aluno->foto = 'fotos/' . $name;
            }

            $aluno->save();
        }

        return redirect()->route('aluno.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $aluno = Aluno::find($id);
        Gate::authorize('delete', $aluno);

        if ($aluno) {

            // Remover foto antes de deletar o registro
            if ($aluno->foto && Storage::disk('public')->exists($aluno->foto)) {
                Storage::disk('public')->delete($aluno->foto);
            }

            $aluno->delete();
        }

        return redirect()->route('aluno.index');
    }

    /**
     * Generate PDF report.
     */
    public function report()
    {
        $alunos = Aluno::with('curso')->orderBy('curso_id')->get();

        $pdf = Pdf::loadView('aluno.report', compact('alunos'));

        return $pdf->stream('alunos.pdf');
    }
}