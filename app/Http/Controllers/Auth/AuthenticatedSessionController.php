<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Events\AuthenticationEvent;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    event(new AuthenticationEvent(Auth::user()->role_id));

    // ===========================
    // DEFINE TIPO DE USUÁRIO
    // ===========================
    if (Auth::user()->role_id == 1) {
        // Login de cliente (professor)
        session(['tipo_usuario' => 'cliente']);
        
        // IMPORTANTE: não salvar nome de cliente aqui
        // O nome REAL virá do popup
        session()->forget('cliente_nome');
    } 
    else {
        // Funcionário / coordenador
        session(['tipo_usuario' => 'funcionario']);

        // Funcionário nunca terá popup → garantimos isso
        session()->forget('cliente_nome');
    }

    return redirect()->intended(route('produto.index', absolute: false));
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
