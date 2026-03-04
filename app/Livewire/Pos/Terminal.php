<?php

namespace App\Livewire\Pos;

use App\Models\CashSession;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Terminal extends Component
{
    public string $search = '';
    public ?int $selectedCategory = null;
    public array $cart = [];
    public string $paymentMethod = 'cash';
    public float $discountAmount = 0;
    public bool $showPaymentModal = false;
    public bool $showReceiptModal = false;
    public ?Sale $lastSale = null;
    public ?CashSession $session = null;

    public function mount(): void
    {
        $this->session = Auth::user()->activeSession;

        if (!$this->session) {
            $this->redirect(route('pos.session'), navigate: true);
        }
    }

    public function getCategoriesProperty()
    {
        return Category::whereHas('products', fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();
    }

    public function getProductsProperty()
    {
        $query = Product::active()->with('category');

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if (strlen($this->search) >= 1) {
            $query->search($this->search);
        }

        return $query->orderBy('name')->limit(48)->get();
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->selectedCategory = ($this->selectedCategory === $categoryId) ? null : $categoryId;
        $this->search = '';
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_active) {
            return;
        }

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] >= $product->stock) {
                session()->flash('error', 'Insufficient stock.');
                return;
            }
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['subtotal'] = round($this->cart[$productId]['quantity'] * $this->cart[$productId]['unit_price'], 2);
        } else {
            if ($product->stock < 1) {
                session()->flash('error', 'Product out of stock.');
                return;
            }
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'unit_price' => (float) $product->price,
                'quantity' => 1,
                'subtotal' => (float) $product->price,
                'stock' => $product->stock,
            ];
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->removeFromCart($productId);
            return;
        }

        $product = Product::find($productId);
        if ($quantity > $product->stock) {
            session()->flash('error', 'Insufficient stock.');
            return;
        }

        $this->cart[$productId]['quantity'] = $quantity;
        $this->cart[$productId]['subtotal'] = round($quantity * $this->cart[$productId]['unit_price'], 2);
    }

    public function getSubtotalProperty(): float
    {
        return round(array_sum(array_column($this->cart, 'subtotal')), 2);
    }

    public function getTotalProperty(): float
    {
        return round($this->subtotal - $this->discountAmount, 2);
    }

    public function getItemCountProperty(): int
    {
        return array_sum(array_column($this->cart, 'quantity'));
    }

    public function openPaymentModal(): void
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty.');
            return;
        }
        $this->showPaymentModal = true;
    }

    public function completeSale(): void
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty.');
            return;
        }

        if ($this->discountAmount > $this->subtotal) {
            $this->addError('discountAmount', 'Discount cannot exceed the subtotal.');
            return;
        }

        DB::transaction(function () {
            $sale = Sale::create([
                'cashier_id' => Auth::id(),
                'session_id' => $this->session->id,
                'total_amount' => $this->total,
                'discount_amount' => $this->discountAmount,
                'payment_method' => $this->paymentMethod,
                'status' => 'completed',
            ]);

            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Decrement stock
                Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
            }

            // Update session totals
            $this->session->recalculateTotals();

            $this->lastSale = $sale->fresh(['items.product', 'cashier']);
        });

        $this->cart = [];
        $this->discountAmount = 0;
        $this->showPaymentModal = false;
        $this->showReceiptModal = true;
    }

    public function cancelSale(): void
    {
        $this->cart = [];
        $this->discountAmount = 0;
        $this->showPaymentModal = false;
    }

    public function closeReceipt(): void
    {
        $this->showReceiptModal = false;
        $this->lastSale = null;
    }

    public function render()
    {
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.pos.terminal');

        return $view->layout('components.layouts.pos');
    }
}
