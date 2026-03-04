<?php

namespace App\Livewire\Pos;

use App\Models\CashSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SessionManager extends Component
{
    public string $openingAmount = '';
    public string $closingAmount = '';
    public ?CashSession $activeSession = null;

    public function mount(): void
    {
        $this->activeSession = Auth::user()->activeSession;
    }

    public function openSession(): void
    {
        $this->validate([
            'openingAmount' => 'required|numeric|min:0',
        ]);

        if ($this->activeSession) {
            session()->flash('error', 'You already have an open session.');
            return;
        }

        $this->activeSession = CashSession::create([
            'cashier_id' => Auth::id(),
            'opening_amount' => $this->openingAmount,
            'opened_at' => now(),
        ]);

        $this->openingAmount = '';
        session()->flash('success', 'Cash session opened successfully!');

        $this->redirect(route('pos.terminal'), navigate: true);
    }

    public function closeSession(): void
    {
        $this->validate([
            'closingAmount' => 'required|numeric|min:0',
        ]);

        if (!$this->activeSession) {
            session()->flash('error', 'No active session found.');
            return;
        }

        $this->activeSession->recalculateTotals();

        $this->activeSession->update([
            'closing_amount' => $this->closingAmount,
            'closed_at' => now(),
        ]);

        $this->activeSession = null;
        $this->closingAmount = '';

        session()->flash('success', 'Session closed successfully!');
        $this->redirect(route('pos.session'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pos.session-manager')
            ->layout('components.layouts.pos');
    }
}
