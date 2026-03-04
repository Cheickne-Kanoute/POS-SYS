<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CashierLogin extends Component
{
    public string $pin = '';
    public ?int $selectedCashierId = null;

    public function getCashiersProperty()
    {
        return User::where('role', 'cashier')->where('is_active', true)->get();
    }

    public function selectCashier(int $id): void
    {
        $this->selectedCashierId = $id;
        $this->pin = '';
    }

    public function appendPin(string $digit): void
    {
        if (strlen($this->pin) < 4) {
            $this->pin .= $digit;
        }

        if (strlen($this->pin) === 4) {
            $this->login();
        }
    }

    public function clearPin(): void
    {
        $this->pin = '';
    }

    public function backspacePin(): void
    {
        $this->pin = substr($this->pin, 0, -1);
    }

    public function login(): void
    {
        if (!$this->selectedCashierId) {
            $this->addError('pin', 'Please select a cashier first.');
            return;
        }

        $cashier = User::find($this->selectedCashierId);

        if (!$cashier || !$cashier->verifyPin($this->pin)) {
            $this->pin = '';
            $this->addError('pin', 'Incorrect PIN. Please try again.');
            return;
        }

        Auth::login($cashier);
        session()->regenerate();

        $this->redirect(route('pos.session'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.cashier-login')
            ->layout('components.layouts.guest');
    }
}
