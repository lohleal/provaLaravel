<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\CursoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('home');
})->name('home')->middleware(['auth', 'verified']);

Route::resource('/curso', CursoController::class)->middleware(['auth', 'verified']);

Route::resource('/aluno', AlunoController::class)->middleware(['auth', 'verified']);
Route::get('/report/aluno', [AlunoController::class, 'report'])->name('report.aluno')->middleware(['auth', 'verified']);

Route::post('/carrinho/update/{id}', [App\Http\Controllers\CarrinhoController::class, 'update']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/cliente/store', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'nome' => 'required|string|max:50',
    ]);

    $id = uniqid(); // ID único EXCLUSIVO do cliente da vez

    session([
        'cliente_id' => $id,
        'cliente_nome' => $request->nome,
    ]);

    return response()->json(['success' => true]);
});

Route::post('/pedido/finalizar', [App\Http\Controllers\PedidoController::class, 'finalizar'])->name('pedido.finalizar');



require __DIR__.'/auth.php';