<?php

namespace App\Livewire;

use App\Models\CashSession;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function getTodayRevenueProperty(): float
    {
        return (float) Sale::completed()->today()->sum('total_amount');
    }

    public function getTodaySalesCountProperty(): int
    {
        return Sale::completed()->today()->count();
    }

    public function getTotalProductsProperty(): int
    {
        return Product::count();
    }

    public function getTotalUsersProperty(): int
    {
        return User::where('is_active', true)->count();
    }

    public function getOpenSessionsProperty()
    {
        return CashSession::with('cashier')->whereNull('closed_at')->get();
    }

    public function getRecentSalesProperty()
    {
        return Sale::with(['cashier', 'items'])
            ->completed()
            ->today()
            ->latest()
            ->take(5)
            ->get();
    }

    public function getLowStockProductsProperty()
    {
        return Product::where('stock', '<=', 5)->where('is_active', true)->take(5)->get();
    }

    public function render()
    {
        return view('livewire.admin-dashboard')
            ->layout('components.layouts.app');
    }
}
