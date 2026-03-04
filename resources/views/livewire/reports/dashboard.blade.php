<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Sales Reports</h1>
            <p class="text-slate-400 text-sm mt-1">Track performance and manage transactions</p>
        </div>
    </div>

    {{-- Date Filters --}}
    <div class="flex flex-wrap gap-3 mb-8 p-4 bg-slate-900 border border-slate-800 rounded-2xl">
        <div class="flex items-center gap-2">
            <label class="text-xs text-slate-400 font-medium">From</label>
            <input wire:model.live="dateFrom" type="date"
                class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex items-center gap-2">
            <label class="text-xs text-slate-400 font-medium">To</label>
            <input wire:model.live="dateTo" type="date"
                class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex items-center gap-2">
            <label class="text-xs text-slate-400 font-medium">Cashier</label>
            <select wire:model.live="filterCashier"
                class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Cashiers</option>
                @foreach($this->cashiers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <p class="text-xs text-slate-500 mb-2">Total Revenue</p>
            <p class="text-2xl font-bold text-white">{{ number_format($this->totalRevenue, 0, '.', ' ') }} <span
                    class="text-sm font-normal text-slate-400">F</span></p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <p class="text-xs text-slate-500 mb-2">Transactions</p>
            <p class="text-2xl font-bold text-white">{{ $this->totalSalesCount }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <p class="text-xs text-slate-500 mb-2">Cash Sales</p>
            <p class="text-2xl font-bold text-emerald-400">{{ number_format($this->totalCash, 0, '.', ' ') }} F</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <p class="text-xs text-slate-500 mb-2">Card + Mobile</p>
            <p class="text-2xl font-bold text-blue-400">
                {{ number_format($this->totalCard + $this->totalMobileMoney, 0, '.', ' ') }} F</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Sales by Cashier --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800">
                <h2 class="text-sm font-semibold text-white">Cashier Performance</h2>
            </div>
            <div class="divide-y divide-slate-800">
                @forelse($this->salesByCashier as $row)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-7 h-7 bg-indigo-700 rounded-full flex items-center justify-center text-xs font-bold text-white">
                                    {{ $row->cashier->initials() }}</div>
                                <p class="text-sm font-medium text-white">{{ $row->cashier->name }}</p>
                            </div>
                            <span class="text-xs text-slate-500">{{ $row->total_transactions }} sales</span>
                        </div>
                        <p class="text-sm font-bold text-indigo-400 ml-9">
                            {{ number_format($row->total_revenue, 0, '.', ' ') }} F</p>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-slate-500 text-sm">No data for this period.</div>
                @endforelse
            </div>
        </div>

        {{-- Sessions --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800">
                <h2 class="text-sm font-semibold text-white">Cash Sessions</h2>
            </div>
            <div class="divide-y divide-slate-800 max-h-96 overflow-y-auto">
                @forelse($this->sessions as $session)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-white">{{ $session->cashier->name }}</p>
                            <span @class(['text-xs px-2 py-0.5 rounded-full font-medium', 'bg-emerald-900/40 text-emerald-400' => $session->isOpen(), 'bg-slate-800 text-slate-500' => !$session->isOpen()])>
                                {{ $session->isOpen() ? 'Open' : 'Closed' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">{{ $session->opened_at->format('d/m H:i') }}
                            @unless($session->isOpen())→ {{ $session->closed_at->format('H:i') }} @endunless</p>
                        <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                            <div class="bg-slate-800 rounded-lg p-2">
                                <span class="text-slate-500">Opening</span>
                                <p class="font-semibold text-white">
                                    {{ number_format($session->opening_amount, 0, '.', ' ') }} F</p>
                            </div>
                            <div class="bg-slate-800 rounded-lg p-2">
                                <span class="text-slate-500">Sales</span>
                                <p class="font-semibold text-emerald-400">
                                    {{ number_format($session->total_sales, 0, '.', ' ') }} F</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-slate-500 text-sm">No sessions for this period.</div>
                @endforelse
            </div>
        </div>

        {{-- Payment Breakdown --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800">
                <h2 class="text-sm font-semibold text-white">Payment Breakdown</h2>
            </div>
            <div class="p-6 space-y-4">
                @php $total = $this->totalRevenue ?: 1; @endphp
                @foreach([['label' => 'Cash', 'value' => $this->totalCash, 'color' => 'bg-emerald-500', 'text' => 'text-emerald-400'], ['label' => 'Card', 'value' => $this->totalCard, 'color' => 'bg-blue-500', 'text' => 'text-blue-400'], ['label' => 'Mobile Money', 'value' => $this->totalMobileMoney, 'color' => 'bg-amber-500', 'text' => 'text-amber-400']] as $method)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-400">{{ $method['label'] }}</span>
                            <span
                                class="{{ $method['text'] }} font-semibold">{{ number_format($method['value'], 0, '.', ' ') }}
                                F</span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-2">
                            <div class="{{ $method['color'] }} h-2 rounded-full transition-all duration-500"
                                style="width: {{ round(($method['value'] / $total) * 100) }}%"></div>
                        </div>
                        <p class="text-xs text-slate-600 mt-0.5">{{ round(($method['value'] / $total) * 100) }}%</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Sales Table --}}
    <div class="mt-6 bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800">
            <h2 class="text-sm font-semibold text-white">Recent Transactions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">#
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Date / Time</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Cashier</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Payment</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Amount</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Status</th>
                        @if(auth()->user()->isAdminOrManager())
                            <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($this->recentSales as $sale)
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-3 text-sm text-slate-500 font-mono">#{{ $sale->id }}</td>
                            <td class="px-6 py-3 text-sm text-white">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 text-sm text-slate-400">{{ $sale->cashier->name }}</td>
                            <td class="px-6 py-3">
                                <span @class(['text-xs px-2 py-0.5 rounded-full font-medium', 'bg-emerald-900/40 text-emerald-400' => $sale->payment_method === 'cash', 'bg-blue-900/40 text-blue-400' => $sale->payment_method === 'card', 'bg-amber-900/40 text-amber-400' => $sale->payment_method === 'mobile_money'])>
                                    {{ str_replace('_', ' ', ucfirst($sale->payment_method)) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm font-semibold text-white text-right">
                                {{ number_format($sale->total_amount, 0, '.', ' ') }} F</td>
                            <td class="px-6 py-3">
                                <span @class(['text-xs px-2 py-0.5 rounded-full font-medium', 'bg-emerald-900/40 text-emerald-400' => $sale->status === 'completed', 'bg-red-900/40 text-red-400' => $sale->status === 'cancelled', 'bg-amber-900/40 text-amber-400' => $sale->status === 'refunded'])>
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            @if(auth()->user()->isAdminOrManager())
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2 justify-end">
                                        @if($sale->status === 'completed')
                                            <button wire:click="cancelSale({{ $sale->id }})"
                                                wire:confirm="Cancel sale #{{ $sale->id }}? Stock will be restored."
                                                class="px-2.5 py-1 bg-red-900/40 hover:bg-red-800/50 text-red-400 text-xs font-medium rounded-lg transition-colors">Cancel</button>
                                            <button wire:click="refundSale({{ $sale->id }})"
                                                wire:confirm="Refund sale #{{ $sale->id }}? Stock will be restored."
                                                class="px-2.5 py-1 bg-amber-900/40 hover:bg-amber-800/50 text-amber-400 text-xs font-medium rounded-lg transition-colors">Refund</button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500 text-sm">No transactions for this
                                period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>