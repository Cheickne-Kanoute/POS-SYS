<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_id',
        'opening_amount',
        'closing_amount',
        'total_sales',
        'total_cash',
        'total_card',
        'total_mobile_money',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_amount' => 'decimal:2',
            'closing_amount' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'total_cash' => 'decimal:2',
            'total_card' => 'decimal:2',
            'total_mobile_money' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'session_id');
    }

    public function isOpen(): bool
    {
        return is_null($this->closed_at);
    }

    public function recalculateTotals(): void
    {
        $sales = $this->sales()->where('status', 'completed')->get();

        $this->total_sales = $sales->sum('total_amount');
        $this->total_cash = $sales->where('payment_method', 'cash')->sum('total_amount');
        $this->total_card = $sales->where('payment_method', 'card')->sum('total_amount');
        $this->total_mobile_money = $sales->where('payment_method', 'mobile_money')->sum('total_amount');
        $this->save();
    }
}
