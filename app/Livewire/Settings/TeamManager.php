<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Title('Gestão da Equipe & Permissões')]
#[Layout('layouts.app')]
class TeamManager extends Component
{
    public bool $modalOpen = false;
    public ?int $editingUserId = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $role = 'assistant';

    public function mount(): void
    {
        // Trava de segurança: apenas admin ou broker podem acessar a gestão de equipe
        if (! Auth::user()?->canManageTeam()) {
            abort(403, 'Você não possui permissão de gestor para acessar as configurações de equipe.');
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingUserId', 'name', 'email', 'phone', 'password', 'role']);
        $this->role = 'assistant';
        $this->modalOpen = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::where('tenant_id', Auth::user()->tenant_id)->findOrFail($userId);
        
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->password = '';
        $this->role = $user->hasRole('broker') ? 'broker' : 'assistant';
        $this->modalOpen = true;
    }

    public function saveMember(): void
    {
        $tenantId = Auth::user()->tenant_id;

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                $this->editingUserId 
                    ? Rule::unique('users', 'email')->ignore($this->editingUserId)
                    : Rule::unique('users', 'email'),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'role'  => ['required', 'in:broker,assistant,consultant'],
        ];

        if (! $this->editingUserId) {
            $rules['password'] = ['required', 'string', 'min:6'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:6'];
        }

        $this->validate($rules, [
            'name.required'     => 'Informe o nome completo do membro.',
            'email.required'    => 'Informe o e-mail corporativo.',
            'email.unique'      => 'Este e-mail já está em uso.',
            'password.required' => 'Defina uma senha de acesso.',
            'password.min'      => 'A senha deve ter no mínimo 6 caracteres.',
        ]);

        if ($this->editingUserId) {
            $user = User::where('tenant_id', $tenantId)->findOrFail($this->editingUserId);
            $updateData = [
                'name'  => $this->name,
                'email' => $this->email,
                'phone' => $this->phone ?: null,
            ];

            if (filled($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $user->update($updateData);

            // Sincroniza Role
            $roleName = $this->role === 'broker' ? 'broker' : 'assistant';
            if (Role::where('name', $roleName)->exists()) {
                $user->syncRoles([$roleName]);
            }

            Notification::make()
                ->title('Membro da Equipe Atualizado!')
                ->success()
                ->send();
        } else {
            $user = User::create([
                'tenant_id' => $tenantId,
                'name'      => $this->name,
                'email'     => $this->email,
                'phone'     => $this->phone ?: null,
                'password'  => Hash::make($this->password),
                'is_active' => true,
            ]);

            $roleName = $this->role === 'broker' ? 'broker' : 'assistant';
            if (Role::where('name', $roleName)->exists()) {
                $user->assignRole($roleName);
            }

            Notification::make()
                ->title('Novo Membro Adicionado!')
                ->body("O acesso para {$user->name} foi configurado com sucesso.")
                ->success()
                ->send();
        }

        $this->reset(['modalOpen', 'editingUserId', 'name', 'email', 'phone', 'password', 'role']);
    }

    public function toggleUserStatus(int $userId): void
    {
        $tenantId = Auth::user()->tenant_id;
        $user = User::where('tenant_id', $tenantId)->findOrFail($userId);

        // Previne inativação do próprio usuário logado
        if ($user->id === Auth::id()) {
            Notification::make()
                ->title('Ação Não Permitida')
                ->body('Você não pode inativar a sua própria conta de gestor.')
                ->danger()
                ->send();
            return;
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        $statusLabel = $user->is_active ? 'ativada' : 'inativada';

        Notification::make()
            ->title("Conta {$statusLabel} com sucesso!")
            ->info()
            ->send();
    }

    public function deleteMember(int $userId): void
    {
        $tenantId = Auth::user()->tenant_id;
        $user = User::where('tenant_id', $tenantId)->findOrFail($userId);

        if ($user->id === Auth::id()) {
            Notification::make()
                ->title('Ação Não Permitida')
                ->body('Você não pode excluir a sua própria conta.')
                ->danger()
                ->send();
            return;
        }

        $user->delete();

        Notification::make()
            ->title('Membro da Equipe Removido')
            ->warning()
            ->send();
    }

    public function getMembersProperty(): Collection
    {
        $tenantId = Auth::user()?->tenant_id;

        return User::query()
            ->where('tenant_id', $tenantId)
            ->with('roles')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.settings.team-manager', [
            'members' => $this->members,
        ]);
    }
}
