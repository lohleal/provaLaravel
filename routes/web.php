<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PedidoController;

Route::get('/', fn() => view('welcome'));

Route::get('/home', fn() => view('home'))
    ->name('home')
    ->middleware(['auth', 'verified']);

Route::resource('/curso', CursoController::class)->middleware(['auth', 'verified']);
Route::resource('/aluno', AlunoController::class)->middleware(['auth', 'verified']);
Route::get('/report/aluno', [AlunoController::class, 'report'])->name('report.aluno')->middleware(['auth', 'verified']);

Route::post('/cliente/store', [ClienteController::class, 'store'])->name('cliente.store');
Route::post('/carrinho/update/{id}', [CarrinhoController::class, 'update'])->name('carrinho.update');
Route::post('/pedido/finalizar', [PedidoController::class, 'finalizar'])->name('pedido.finalizar');

Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/pedido/revisar', [PedidoController::class, 'revisar'])->name('pedido.revisar');
Route::get('/pedidos', [PedidoController::class, 'lista'])
    ->middleware(['auth'])
    ->name('pedido.lista');

Route::get('/pedidos/{id}/pdf', [PedidoController::class, 'pdf'])
    ->middleware(['auth'])
    ->name('pedido.pdf');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
