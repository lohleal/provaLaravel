<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarrinhoController extends Controller
{
    public function update($id, Request $request)
    {
        $cart = session()->get('cart', []);

        // Se ainda não existe, começa com 0
        if (!isset($cart[$id])) {
            $cart[$id] = 0;
        }

        if ($request->action === 'increase') {
            $cart[$id]++;
        }

        if ($request->action === 'decrease' && $cart[$id] > 0) {
            $cart[$id]--;
        }

        session()->put('cart', $cart);

        return response()->json([
            'quantidade' => $cart[$id]
        ]);
    }
}