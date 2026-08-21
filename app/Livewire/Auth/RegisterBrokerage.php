<?php

namespace App\Livewire\Auth;

use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Title('Cadastrar Corretora | Salut Royale')]
#[Layout('layouts.auth')]
class RegisterBrokerage extends Component
{
    // Dados da Corretora
    public string $brokerage_name = '';
    public string $cnpj = '';
    public string $brokerage_phone = '';

    // Dados do Corretor Administrador
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Hooks de ciclo de vida Livewire para higienização em tempo real.
     */
    public function updatedEmail($value): void
    {
        $this->email = trim(strtolower((string) $value));
    }

    public function updatedCnpj($value): void
    {
        $this->cnpj = trim((string) $value);
    }

    public function updatedBrokeragePhone($value): void
    {
        $this->brokerage_phone = trim((string) $value);
    }

    protected function rules(): array
    {
        return [
            'brokerage_name'  => ['required', 'string', 'min:3', 'max:255'],
            'cnpj'            => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail) {
                    $clean = preg_replace('/\D/', '', (string) $value);
                    if (strlen($clean) !== 14) {
                        $fail('O CNPJ deve conter 14 dígitos no formato 00.000.000/0000-00.');
                        return;
                    }

                    if (Tenant::where('document', $clean)->exists()) {
                        $fail('Este CNPJ já está cadastrado em outra corretora.');
                    }
                },
            ],
            'brokerage_phone' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail) {
                    $clean = preg_replace('/\D/', '', (string) $value);
                    if (strlen($clean) < 10 || strlen($clean) > 11) {
                        $fail('Informe um telefone ou WhatsApp válido com DDD (10 ou 11 dígitos).');
                    }
                },
            ],
            'name'            => ['required', 'string', 'min:3', 'max:255'],
            'email'           => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'password'        => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    protected function messages(): array
    {
        return [
            'brokerage_name.required' => 'Informe a Razão Social ou Nome Fantasia da sua Corretora.',
            'brokerage_name.min'      => 'O nome da corretora deve ter pelo menos 3 caracteres.',
            'cnpj.required'           => 'Informe o CNPJ da sua corretora.',
            'brokerage_phone.required'=> 'Informe o Telefone ou WhatsApp corporativo.',
            'name.required'           => 'Informe o seu nome completo.',
            'email.required'          => 'Informe o seu e-mail corporativo de acesso.',
            'email.email'             => 'Informe um endereço de e-mail válido.',
            'email.unique'            => 'Este e-mail já está cadastrado no sistema.',
            'password.required'       => 'Crie uma senha de acesso.',
            'password.min'            => 'A senha deve conter no mínimo 6 caracteres.',
            'password.confirmed'      => 'A confirmação de senha não confere.',
        ];
    }

    public function register()
    {
        // Higienização inicial antes da validação
        $this->email = trim(strtolower((string) $this->email));
        $this->name = trim((string) $this->name);
        $this->brokerage_name = trim((string) $this->brokerage_name);

        $this->validate();

        $user = DB::transaction(function () {
            // 1. Higienização dos campos para persistência segura
            $cleanDocument = preg_replace('/\D/', '', (string) $this->cnpj);
            $cleanPhone = preg_replace('/\D/', '', (string) $this->brokerage_phone);
            $cleanEmail = trim(strtolower((string) $this->email));
            $cleanName = trim((string) $this->name);
            $cleanBrokerageName = trim((string) $this->brokerage_name);

            $slugBase = Str::slug($cleanBrokerageName);
            $slug = $slugBase ?: 'corretora';

            if (Tenant::where('slug', $slug)->exists()) {
                $slug .= '-' . substr(uniqid(), -4);
            }

            // Formatação padrão do telefone para exibição amigável
            $formattedPhone = strlen($cleanPhone) === 11
                ? '(' . substr($cleanPhone, 0, 2) . ') ' . substr($cleanPhone, 2, 5) . '-' . substr($cleanPhone, 7)
                : '(' . substr($cleanPhone, 0, 2) . ') ' . substr($cleanPhone, 2, 4) . '-' . substr($cleanPhone, 6);

            // 2. Cria a nova Corretora (Tenant com isolamento de dados)
            $tenant = Tenant::create([
                'name'      => $cleanBrokerageName,
                'slug'      => $slug,
                'email'     => $cleanEmail,
                'document'  => $cleanDocument,
                'phone'     => $formattedPhone,
                'is_active' => true,
            ]);

            // 3. Cria o Corretor Gestor Titular
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name'      => $cleanName,
                'email'     => $cleanEmail,
                'phone'     => $formattedPhone,
                'password'  => Hash::make($this->password),
                'is_active' => true,
            ]);

            // 4. Atribui o perfil de Corretor Gestor (broker)
            if (Role::where('name', 'broker')->exists()) {
                $user->assignRole('broker');
            }

            return $user;
        });

        // 5. Realiza o login automático e redireciona
        Auth::login($user);
        session()->regenerate();

        session()->flash('success', 'Bem-vindo! Sua corretora foi configurada com sucesso.');
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register-brokerage');
    }
}
