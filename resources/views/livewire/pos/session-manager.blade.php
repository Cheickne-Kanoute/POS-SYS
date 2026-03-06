<div class="h-[calc(100vh-3.5rem)] flex flex-col items-center justify-center p-6">
    @if(session('success'))
        <div
            class="fixed top-20 right-6 z-50 px-5 py-3 bg-emerald-700 text-white text-sm rounded-xl shadow-lg border border-emerald-600">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div
            class="fixed top-20 right-6 z-50 px-5 py-3 bg-red-700 text-white text-sm rounded-xl shadow-lg border border-red-600">
            {{ session('error') }}
        </div>
    @endif

    @if($activeSession)
        {{-- Session Open - Close it --}}
        <div class="w-full max-w-lg">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-emerald-600/20 border border-emerald-600/40 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-white">Session Active</h1>
                <p class="text-slate-400 text-sm mt-1">Ouverte à {{ $activeSession->opened_at->format('H:i') }}</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 mb-6">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-800 rounded-xl p-4">
                        <p class="text-xs text-slate-500 mb-1">Fond de Caisse</p>
                        <p class="text-lg font-bold text-white">
                            {{ number_format($activeSession->opening_amount, 0, '.', ' ') }} F
                        </p>
                    </div>
                    <div class="bg-slate-800 rounded-xl p-4">
                        <p class="text-xs text-slate-500 mb-1">Total Ventes</p>
                        <p class="text-lg font-bold text-emerald-400">
                            {{ number_format($activeSession->total_sales, 0, '.', ' ') }} F
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <a href="{{ route('pos.terminal') }}"
                        class="w-full flex items-center justify-center gap-2 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all active:scale-95">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Ouvrir le Terminal de Caisse
                    </a>
                </div>

                <div class="border-t border-slate-700 pt-4">
                    <p class="text-sm font-medium text-slate-300 mb-3">Fermer la session</p>
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <input wire:model="closingAmount" type="number" step="0.01" min="0"
                                placeholder="Montant de clôture en caisse..."
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                            @error('closingAmount') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="closeSession"
                            class="px-4 py-2.5 bg-red-800 hover:bg-red-700 text-white font-medium rounded-xl transition-all text-sm"
                            wire:confirm="Voulez-vous vraiment fermer cette session ?">
                            Fermer la session
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- No session — Open one --}}
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-slate-800 border border-slate-700 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-white">Commencez votre service</h1>
                <p class="text-slate-400 text-sm mt-1">Saisissez le montant de votre fond de caisse pour commencer</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Montant du fond de caisse (F CFA)</label>
                    <input wire:model="openingAmount" type="number" step="0.01" min="0" placeholder="ex: 50000"
                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    @error('openingAmount') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <button wire:click="openSession"
                    class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all active:scale-95"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Ouvrir la caisse</span>
                    <span wire:loading>Ouverture en cours...</span>
                </button>

                <p class="text-xs text-slate-600 text-center mt-3">Vous ne pouvez pas effectuer de ventes sans avoir ouvert
                    la caisse.
                </p>
            </div>
        </div>
    @endif
</div>