<?php

namespace App\Livewire\Reports;

use App\Models\CashSession;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $filterCashier = '';

    public function mount(): void
    {
        $this->dateFrom = today()->toDateString();
        $this->dateTo = today()->toDateString();
    }

    public function getSalesQueryProperty()
    {
        $query = Sale::with(['cashier', 'session'])
            ->completed()
            ->whereBetween('created_at', [
                $this->dateFrom . ' 00:00:00',
                $this->dateTo . ' 23:59:59',
            ]);

        if ($this->filterCashier) {
            $query->where('cashier_id', $this->filterCashier);
        }

        // Managers can only see their own sales
        if (Auth::user()->isManager()) {
            // managers see all for oversight
        }

        return $query;
    }

    public function getTotalRevenueProperty(): float
    {
        return (float) $this->salesQuery->sum('total_amount');
    }

    public function getTotalSalesCountProperty(): int
    {
        return $this->salesQuery->count();
    }

    public function getTotalCashProperty(): float
    {
        return (float) $this->salesQuery->clone()->where('payment_method', 'cash')->sum('total_amount');
    }

    public function getTotalCardProperty(): float
    {
        return (float) $this->salesQuery->clone()->where('payment_method', 'card')->sum('total_amount');
    }

    public function getTotalMobileMoneyProperty(): float
    {
        return (float) $this->salesQuery->clone()->where('payment_method', 'mobile_money')->sum('total_amount');
    }

    public function getSalesByPaymentProperty()
    {
        return [
            'cash' => $this->totalCash,
            'card' => $this->totalCard,
            'mobile_money' => $this->totalMobileMoney,
        ];
    }

    public function getSalesByCashierProperty()
    {
        return $this->salesQuery->clone()
            ->selectRaw('cashier_id, COUNT(*) as total_transactions, SUM(total_amount) as total_revenue')
            ->groupBy('cashier_id')
            ->with('cashier')
            ->get();
    }

    public function getRecentSalesProperty()
    {
        return $this->salesQuery->clone()
            ->with(['cashier', 'items'])
            ->latest()
            ->take(15)
            ->get();
    }

    public function getSessionsProperty()
    {
        return CashSession::with('cashier')
            ->whereBetween('opened_at', [
                $this->dateFrom . ' 00:00:00',
                $this->dateTo . ' 23:59:59',
            ])
            ->when($this->filterCashier, fn($q) => $q->where('cashier_id', $this->filterCashier))
            ->latest()
            ->get();
    }

    public function getCashiersProperty()
    {
        return User::where('role', 'cashier')->orderBy('name')->get();
    }

    public function cancelSale(int $saleId): void
    {
        if (!Auth::user()->isAdminOrManager()) {
            abort(403);
        }

        $sale = Sale::findOrFail($saleId);

        if ($sale->status !== 'completed') {
            session()->flash('error', 'Only completed sales can be cancelled.');
            return;
        }

        $sale->update(['status' => 'cancelled']);

        // Restore stock
        foreach ($sale->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        // Recalculate session totals
        $sale->session->recalculateTotals();

        session()->flash('success', 'Sale #' . $sale->id . ' cancelled.');
    }

    public function refundSale(int $saleId): void
    {
        if (!Auth::user()->isAdminOrManager()) {
            abort(403);
        }

        $sale = Sale::findOrFail($saleId);

        if ($sale->status !== 'completed') {
            session()->flash('error', 'Only completed sales can be refunded.');
            return;
        }

        $sale->update(['status' => 'refunded']);

        // Restore stock
        foreach ($sale->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        $sale->session->recalculateTotals();

        session()->flash('success', 'Sale #' . $sale->id . ' refunded.');
    }

    public function render()
    {
        return view('livewire.reports.dashboard')
            ->layout('components.layouts.app');
    }
}
