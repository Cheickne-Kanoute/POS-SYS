<div>
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-indigo-600/20 to-indigo-900/20 border border-indigo-700/30 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-slate-400 font-medium">Revenus du Jour</p>
                <div class="w-9 h-9 bg-indigo-600/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">{{ number_format($this->todayRevenue, 0, '.', ' ') }} F</p>
            <p class="text-xs text-slate-500 mt-1">Toutes ventes terminées</p>
        </div>

        <div
            class="bg-gradient-to-br from-emerald-600/20 to-emerald-900/20 border border-emerald-700/30 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-slate-400 font-medium">Ventes du Jour</p>
                <div class="w-9 h-9 bg-emerald-600/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">{{ $this->todaySalesCount }}</p>
            <p class="text-xs text-slate-500 mt-1">Transactions traitées</p>
        </div>

        <div class="bg-gradient-to-br from-amber-600/20 to-amber-900/20 border border-amber-700/30 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-slate-400 font-medium">Produits</p>
                <div class="w-9 h-9 bg-amber-600/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">{{ $this->totalProducts }}</p>
            <p class="text-xs text-slate-500 mt-1">Articles au catalogue</p>
        </div>

        <div class="bg-gradient-to-br from-violet-600/20 to-violet-900/20 border border-violet-700/30 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-slate-400 font-medium">Utilisateurs Actifs</p>
                <div class="w-9 h-9 bg-violet-600/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">{{ $this->totalUsers }}</p>
            <p class="text-xs text-slate-500 mt-1">Membres du personnel</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Recent Sales --}}
        <div class="xl:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white">Ventes Récentes</h2>
                <a href="{{ route('reports') }}"
                    class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">Tout voir →</a>
            </div>
            <div class="divide-y divide-slate-800">
                @forelse($this->recentSales as $sale)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-slate-800/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center text-xs font-bold text-slate-400">
                                #{{ $sale->id }}</div>
                            <div>
                                <p class="text-sm font-medium text-white">{{ $sale->cashier->name }}</p>
                                <p class="text-xs text-slate-500">{{ $sale->created_at->format('H:i') }} ·
                                    {{ $sale->items->count() }} article(s)
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-white">
                                {{ number_format($sale->total_amount, 0, '.', ' ') }} F
                            </p>
                            <span @class(['text-xs px-2 py-0.5 rounded-full font-medium', 'bg-emerald-900/40 text-emerald-400' => $sale->payment_method === 'cash', 'bg-blue-900/40 text-blue-400' => $sale->payment_method === 'card', 'bg-amber-900/40 text-amber-400' => $sale->payment_method === 'mobile_money'])>
                                {{ str_replace('_', ' ', ucfirst($sale->payment_method)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-slate-500 text-sm">Aucune vente aujourd'hui pour le moment.</div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            {{-- Open Sessions --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800">
                    <h2 class="text-sm font-semibold text-white">Sessions Ouvertes</h2>
                </div>
                <div class="divide-y divide-slate-800">
                    @forelse($this->openSessions as $session)
                        <div class="px-6 py-3">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                <p class="text-sm font-medium text-white">{{ $session->cashier->name }}</p>
                            </div>
                            <p class="text-xs text-slate-500">Depuis {{ $session->opened_at->format('H:i') }}</p>
                            <p class="text-xs text-emerald-400 font-medium mt-0.5">
                                {{ number_format($session->total_sales, 0, '.', ' ') }} F vendus
                            </p>
                        </div>
                    @empty
                        <div class="px-6 py-5 text-center text-slate-500 text-sm">Aucune session active.</div>
                    @endforelse
                </div>
            </div>

            {{-- Low Stock --}}
            @if($this->lowStockProducts->count() > 0)
                <div class="bg-slate-900 border border-amber-800/30 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-amber-800/30 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h2 class="text-sm font-semibold text-amber-400">Alerte Stock Faible</h2>
                    </div>
                    <div class="divide-y divide-slate-800">
                        @foreach($this->lowStockProducts as $product)
                            <div class="px-6 py-3 flex items-center justify-between">
                                <p class="text-sm text-white">{{ Str::limit($product->name, 25) }}</p>
                                <span class="text-xs font-bold text-red-400 bg-red-900/30 px-2 py-0.5 rounded-full">Il en reste
                                    {{ $product->stock }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>