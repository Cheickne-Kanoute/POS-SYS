<div class="h-[calc(100vh-3.5rem)] flex overflow-hidden">

    {{-- LEFT PANEL: Products --}}
    <div class="flex-1 flex flex-col bg-slate-950 border-r border-slate-800 min-w-0">

        {{-- Top Bar: Search + Scanner --}}
        <div class="p-3 border-b border-slate-800 shrink-0">
            <div class="relative">
                {{-- Icône scanner ou loupe --}}
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.200ms="search"
                    wire:keydown.enter="scanBarcode"
                    type="text"
                    placeholder="Rechercher ou scanner un code-barres (Entrée)..."
                    autofocus
                    class="w-full pl-10 pr-10 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                    id="pos-search"
                >
                {{-- Icône scanner à droite --}}
                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 14v1M4 12H3m18 0h-1M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707.707M17.657 6.343l-.707.707"/>
                    </svg>
                </div>
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-8 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>

            {{-- Scan Feedback --}}
            @if($scanFeedback)
                <div @class([
                    'mt-2 px-3 py-2 rounded-lg text-xs font-medium flex items-center gap-2 transition-all',
                    'bg-emerald-900/40 border border-emerald-700 text-emerald-300' => $scanSuccess,
                    'bg-red-900/40 border border-red-700 text-red-300' => !$scanSuccess,
                ])>
                    @if($scanSuccess)
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                    {{ $scanFeedback }}
                </div>
            @endif
        </div>

        {{-- Category Tabs --}}
        @if($this->categories->isNotEmpty())
            <div class="flex gap-2 px-3 py-2 border-b border-slate-800 overflow-x-auto shrink-0 scrollbar-hide">
                <button
                    wire:click="selectCategory(null)"
                    @class([
                        'shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all',
                        'bg-indigo-600 text-white' => $selectedCategory === null,
                        'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white' => $selectedCategory !== null,
                    ])
                >
                    Tous
                </button>
                @foreach($this->categories as $category)
                    <button
                        wire:click="selectCategory({{ $category->id }})"
                        @class([
                            'shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all whitespace-nowrap',
                            'bg-indigo-600 text-white' => $selectedCategory === $category->id,
                            'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white' => $selectedCategory !== $category->id,
                        ])
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Flash messages --}}
        @if(session('error'))
            <div class="mx-3 mt-2 px-4 py-2 bg-red-900/40 border border-red-700 rounded-lg text-red-300 text-xs shrink-0">
                {{ session('error') }}
            </div>
        @endif

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto p-3">
            @if($this->products->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-slate-600">
                    <svg class="w-12 h-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium">Aucun produit trouvé</p>
                    @if($search)
                        <p class="text-xs text-slate-700 mt-1">pour "{{ $search }}"</p>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2.5">
                    @foreach($this->products as $product)
                        <button
                            wire:click="addToCart({{ $product->id }})"
                            @class([
                                'relative text-left p-3 rounded-xl border transition-all duration-150 active:scale-95 group',
                                'bg-slate-900 border-slate-800 hover:border-indigo-500/60 hover:bg-slate-800/80 hover:shadow-lg hover:shadow-indigo-900/20' => $product->stock > 0,
                                'bg-slate-900/40 border-slate-800/40 opacity-50 cursor-not-allowed' => $product->stock <= 0,
                            ])
                            @if($product->stock <= 0) disabled @endif
                        >
                            {{-- Cart badge if already in cart --}}
                            @if(isset($cart[$product->id]))
                                <span class="absolute top-2 right-2 w-5 h-5 bg-indigo-600 rounded-full text-white text-[10px] font-bold flex items-center justify-center">
                                    {{ $cart[$product->id]['quantity'] }}
                                </span>
                            @endif

                            {{-- Icon --}}
                            <div @class([
                                'w-9 h-9 rounded-lg flex items-center justify-center mb-2.5',
                                'bg-indigo-600/20 group-hover:bg-indigo-600/30 transition-colors' => $product->stock > 0,
                                'bg-slate-800/60' => $product->stock <= 0,
                            ])>
                                <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>

                            {{-- Name --}}
                            <p class="text-xs font-semibold text-white leading-tight mb-1 line-clamp-2" style="min-height: 2rem">{{ $product->name }}</p>

                            {{-- Category --}}
                            @if($product->category)
                                <p class="text-[10px] text-slate-600 mb-1.5">{{ $product->category->name }}</p>
                            @endif

                            {{-- Price & Stock --}}
                            <div class="flex items-end justify-between gap-1">
                                <span class="text-xs font-bold text-indigo-400">{{ number_format($product->price, 0, '.', ' ') }} F</span>
                                <span @class([
                                    'text-[10px] px-1.5 py-0.5 rounded font-medium',
                                    'bg-emerald-900/40 text-emerald-400' => $product->stock > 5,
                                    'bg-amber-900/40 text-amber-400' => $product->stock > 0 && $product->stock <= 5,
                                    'bg-red-900/40 text-red-400' => $product->stock <= 0,
                                ])>
                                    {{ $product->stock > 0 ? $product->stock.' stk' : 'Épuisé' }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Session info bar --}}
        <div class="px-4 py-2 border-t border-slate-800 flex items-center justify-between text-xs text-slate-600 shrink-0">
            <span>Session #{{ $session?->id }}</span>
            <span class="text-slate-700">{{ $this->products->count() }} produit(s)</span>
            <a href="{{ route('pos.session') }}" class="text-amber-500 hover:text-amber-400 transition-colors">Fermer session</a>
        </div>
    </div>

    {{-- RIGHT PANEL: Cart --}}
    <div class="w-80 xl:w-96 flex flex-col bg-slate-900 shrink-0">

        {{-- Cart Header --}}
        <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h2 class="font-semibold text-white text-sm">Panier</h2>
                @if($this->itemCount > 0)
                    <span class="bg-indigo-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $this->itemCount }}</span>
                @endif
            </div>
            @if(!empty($cart))
                <button wire:click="cancelSale" class="text-xs text-red-400 hover:text-red-300 transition-colors" wire:confirm="Vider le panier ?">
                    Tout effacer
                </button>
            @endif
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto divide-y divide-slate-800">
            @forelse($cart as $productId => $item)
                <div class="px-5 py-3">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <p class="text-sm font-medium text-white line-clamp-2 leading-snug flex-1">{{ $item['name'] }}</p>
                        <button wire:click="removeFromCart({{ $productId }})" class="text-red-500 hover:text-red-400 shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <button wire:click="updateQuantity({{ $productId }}, {{ $item['quantity'] - 1 }})" class="w-7 h-7 bg-slate-800 hover:bg-slate-700 rounded-lg flex items-center justify-center text-slate-300 text-sm font-bold transition-colors">−</button>
                            <span class="w-8 text-center text-sm font-semibold text-white">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $productId }}, {{ $item['quantity'] + 1 }})" class="w-7 h-7 bg-slate-800 hover:bg-slate-700 rounded-lg flex items-center justify-center text-slate-300 text-sm font-bold transition-colors">+</button>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500">{{ number_format($item['unit_price'], 0, '.', ' ') }} F × {{ $item['quantity'] }}</p>
                            <p class="text-sm font-bold text-white">{{ number_format($item['subtotal'], 0, '.', ' ') }} F</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full text-slate-700 py-16">
                    <svg class="w-12 h-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm">Panier vide</p>
                    <p class="text-xs text-slate-800 mt-1">Sélectionnez des produits</p>
                </div>
            @endforelse
        </div>

        {{-- Cart Footer --}}
        <div class="border-t border-slate-800 p-4 space-y-3 shrink-0">
            {{-- Discount --}}
            <div class="flex items-center gap-3">
                <label class="text-xs text-slate-400 shrink-0">Remise (F)</label>
                <input
                    wire:model.live="discountAmount"
                    type="number"
                    min="0"
                    placeholder="0"
                    class="flex-1 px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 text-sm text-right"
                >
            </div>

            {{-- Totals --}}
            <div class="space-y-1.5">
                <div class="flex justify-between text-sm text-slate-400">
                    <span>Sous-total</span>
                    <span>{{ number_format($this->subtotal, 0, '.', ' ') }} F</span>
                </div>
                @if($discountAmount > 0)
                    <div class="flex justify-between text-sm text-emerald-400">
                        <span>Remise</span>
                        <span>−{{ number_format($discountAmount, 0, '.', ' ') }} F</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-white text-lg border-t border-slate-700 pt-2">
                    <span>Total</span>
                    <span>{{ number_format($this->total, 0, '.', ' ') }} F</span>
                </div>
            </div>

            {{-- Checkout Button --}}
            <button
                wire:click="openPaymentModal"
                class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 disabled:bg-slate-700 disabled:text-slate-500 text-white font-bold rounded-xl transition-all active:scale-95 text-sm"
                @if(empty($cart)) disabled @endif
            >
                Passer au paiement →
            </button>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl">
                <div class="px-6 py-5 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-white">Finaliser le paiement</h2>
                    <button wire:click="cancelSale" class="text-slate-500 hover:text-slate-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <p class="text-sm text-slate-400">Montant à payer</p>
                        <p class="text-4xl font-bold text-white mt-1">{{ number_format($this->total, 0, '.', ' ') }} <span class="text-xl text-slate-400 font-normal">F</span></p>
                    </div>

                    <p class="text-sm font-medium text-slate-300 mb-3">Mode de paiement</p>
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <button
                            wire:click="$set('paymentMethod', 'cash')"
                            @class(['py-4 rounded-xl border-2 font-semibold text-sm transition-all', 'border-emerald-500 bg-emerald-600/20 text-emerald-300' => $paymentMethod === 'cash', 'border-slate-700 bg-slate-800 text-slate-400 hover:border-slate-600' => $paymentMethod !== 'cash'])
                        >
                            💵 Espèces
                        </button>
                        <button
                            wire:click="$set('paymentMethod', 'card')"
                            @class(['py-4 rounded-xl border-2 font-semibold text-sm transition-all', 'border-blue-500 bg-blue-600/20 text-blue-300' => $paymentMethod === 'card', 'border-slate-700 bg-slate-800 text-slate-400 hover:border-slate-600' => $paymentMethod !== 'card'])
                        >
                            💳 Carte
                        </button>
                        <button
                            wire:click="$set('paymentMethod', 'mobile_money')"
                            @class(['py-4 rounded-xl border-2 font-semibold text-sm transition-all', 'border-amber-500 bg-amber-600/20 text-amber-300' => $paymentMethod === 'mobile_money', 'border-slate-700 bg-slate-800 text-slate-400 hover:border-slate-600' => $paymentMethod !== 'mobile_money'])
                        >
                            📱 Mobile
                        </button>
                    </div>

                    <button
                        wire:click="completeSale"
                        class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-all active:scale-95"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>✓ Valider la vente</span>
                        <span wire:loading>Traitement en cours...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- RECEIPT MODAL --}}
    @if($showReceiptModal && $lastSale)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white text-slate-900 rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden">
                {{-- Receipt Header --}}
                <div class="bg-slate-900 text-white px-6 py-4 text-center">
                    <p class="font-bold text-lg">SuperPOS</p>
                    <p class="text-slate-400 text-xs">Reçu officiel</p>
                </div>

                <div class="p-6">
                    <div class="text-center mb-4 pb-4 border-b border-dashed border-slate-300">
                        <p class="text-xs text-slate-500">Reçu #{{ $lastSale->id }}</p>
                        <p class="text-xs text-slate-500">{{ $lastSale->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-xs text-slate-500">Caissier : {{ $lastSale->cashier->name }}</p>
                    </div>

                    {{-- Items --}}
                    <div class="space-y-2 mb-4 pb-4 border-b border-dashed border-slate-300">
                        @foreach($lastSale->items as $item)
                            <div class="flex justify-between text-sm">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $item->product->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $item->quantity }} × {{ number_format($item->unit_price, 0, '.', ' ') }} F</p>
                                </div>
                                <span class="font-semibold">{{ number_format($item->subtotal, 0, '.', ' ') }} F</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totals --}}
                    <div class="space-y-1 mb-4">
                        @if($lastSale->discount_amount > 0)
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Remise</span>
                                <span>−{{ number_format($lastSale->discount_amount, 0, '.', ' ') }} F</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-base text-slate-900">
                            <span>TOTAL</span>
                            <span>{{ number_format($lastSale->total_amount, 0, '.', ' ') }} F</span>
                        </div>
                        <div class="flex justify-between text-sm text-slate-600">
                            <span>Paiement</span>
                            <span>{{ str_replace('_', ' ', ucfirst($lastSale->payment_method)) }}</span>
                        </div>
                    </div>

                    <div class="text-center pt-2 pb-1 border-t border-dashed border-slate-300">
                        <p class="text-xs text-slate-400">Merci pour votre achat !</p>
                        <p class="text-xs text-slate-300">★★★★★</p>
                    </div>
                </div>

                <div class="flex gap-3 px-6 pb-5">
                    <button onclick="window.print()" class="flex-1 py-2.5 bg-slate-900 text-white text-sm font-medium rounded-xl hover:bg-slate-800 transition-colors">
                        🖨️ Imprimer
                    </button>
                    <button wire:click="closeReceipt" class="flex-1 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-500 transition-colors">
                        Nouvelle vente →
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        let feedbackTimer = null;

        function clearFeedback() {
            if (feedbackTimer) clearTimeout(feedbackTimer);
            feedbackTimer = setTimeout(() => {
                @this.set('scanFeedback', '');
                // Refocus the search input
                const input = document.getElementById('pos-search');
                if (input) input.focus();
            }, 2000);
        }

        Livewire.on('scan-success', () => clearFeedback());
        Livewire.on('scan-error', () => clearFeedback());
    });
</script>
