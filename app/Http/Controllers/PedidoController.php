<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function finalizar(Request $request)
    {
        session()->forget(['cliente_id', 'cliente_nome', 'cart']);
        return redirect('/home')->with('popup', true);
    }
}