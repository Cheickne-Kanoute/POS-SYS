<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $pin = '';
    public string $role = 'cashier';
    public bool $is_active = true;

    protected function rules(): array
    {
        $emailRule = $this->isEditing
            ? 'nullable|email|unique:users,email,' . $this->editingId
            : 'nullable|email|unique:users,email';

        $pinRule = ($this->role === 'cashier')
            ? ($this->isEditing ? 'nullable|digits:4' : 'required|digits:4')
            : 'nullable';

        $passwordRule = ($this->role !== 'cashier')
            ? ($this->isEditing ? 'nullable|min:6' : 'required|min:6')
            : 'nullable';

        return [
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'password' => $passwordRule,
            'pin' => $pinRule,
            'role' => 'required|in:admin,manager,cashier',
            'is_active' => 'boolean',
        ];
    }

    public function getUsersProperty()
    {
        return User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->orderBy('name')
            ->paginate(10);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editingId = $userId;
        $this->name = $user->name;
        $this->email = $user->email ?? '';
        $this->role = $user->role;
        $this->is_active = $user->is_active;
        $this->password = '';
        $this->pin = '';
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email ?: null,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->pin) {
            $data['pin'] = Hash::make($this->pin);
        }

        if ($this->isEditing) {
            User::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'User updated successfully.');
        } else {
            User::create($data);
            session()->flash('success', 'User created successfully.');
        }

        $this->closeModal();
    }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('success', 'User status updated.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->isEditing = false;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->pin = '';
        $this->role = 'cashier';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.user-management')
            ->layout('components.layouts.app');
    }
}
