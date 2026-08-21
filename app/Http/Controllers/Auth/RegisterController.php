<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /**
     * Exibe o formulário de cadastro de nova corretora / corretor.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Processa o cadastro de uma nova corretora (Tenant) e do corretor administrador.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brokerage_name' => ['required', 'string', 'max:255'],
            'document'       => ['nullable', 'string', 'max:20'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'brokerage_name.required' => 'Informe o nome ou razão social da sua Corretora.',
            'name.required'           => 'Informe o seu nome completo.',
            'email.required'          => 'Informe o seu e-mail de acesso.',
            'email.unique'            => 'Este e-mail já está cadastrado no sistema.',
            'password.required'       => 'Crie uma senha de acesso.',
            'password.min'            => 'A senha deve conter no mínimo 6 caracteres.',
            'password.confirmed'      => 'A confirmação de senha não confere.',
        ]);

        $user = DB::transaction(function () use ($validated) {
            // 1. Cria a nova Corretora (Tenant Isolado)
            $cleanDocument = preg_replace('/\D/', '', (string) ($validated['document'] ?? ''));
            $slugBase = Str::slug($validated['brokerage_name']);
            $slug = $slugBase ?: 'corretora';

            if (Tenant::where('slug', $slug)->exists()) {
                $slug .= '-' . substr(uniqid(), -4);
            }

            $tenant = Tenant::create([
                'name'     => $validated['brokerage_name'],
                'slug'     => $slug,
                'email'    => $validated['email'],
                'document' => $cleanDocument ?: '00000000000100',
                'phone'    => $validated['phone'] ?? null,
            ]);

            // 2. Cria o Corretor Administrador
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
            ]);

            // 3. Atribui o papel de Corretor / Gestor
            if (Role::where('name', 'broker')->exists()) {
                $user->assignRole('broker');
            }

            return $user;
        });

        // Autentica o usuário imediatamente
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Bem-vindo! Sua corretora foi configurada com sucesso.');
    }
}
