<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-2xl">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-emerald-600 rounded-2xl mb-4 shadow-2xl shadow-emerald-900/50">
                <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Cashier Login</h1>
            <p class="text-slate-400 text-sm mt-1">Select your name and enter your 4-digit PIN</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Cashier Selection --}}
            <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Select Cashier</h2>
                <div class="space-y-2">
                    @foreach($this->cashiers as $cashier)
                        <button wire:click="selectCashier({{ $cashier->id }})" @class([
                            'w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-150',
                            'bg-emerald-600 shadow-lg shadow-emerald-900/40' => $selectedCashierId === $cashier->id,
                            'bg-slate-800 hover:bg-slate-700' => $selectedCashierId !== $cashier->id,
                        ])>
                            <div @class([
                                'w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0',
                                'bg-emerald-500 text-white' => $selectedCashierId === $cashier->id,
                                'bg-slate-700 text-slate-300' => $selectedCashierId !== $cashier->id,
                            ])>
                                {{ $cashier->initials() }}
                            </div>
                            <span class="text-sm font-medium text-white text-left">{{ $cashier->name }}</span>
                            @if($selectedCashierId === $cashier->id)
                                <svg class="w-4 h-4 text-emerald-200 ml-auto" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- PIN Pad --}}
            <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Enter PIN</h2>

                {{-- PIN dots --}}
                <div class="flex justify-center gap-4 mb-6">
                    @for($i = 0; $i < 4; $i++)
                        <div @class([
                            'w-12 h-12 rounded-xl border-2 flex items-center justify-center text-2xl font-bold transition-all',
                            'border-emerald-500 bg-emerald-600/20' => strlen($pin) > $i,
                            'border-slate-700 bg-slate-800' => strlen($pin) <= $i,
                        ])>
                            @if(strlen($pin) > $i)
                                <span class="text-emerald-400">●</span>
                            @endif
                        </div>
                    @endfor
                </div>

                @error('pin')
                    <div
                        class="mb-4 text-center text-xs text-red-400 bg-red-900/30 border border-red-800 rounded-lg py-2 px-3">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Numpad --}}
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['1', '2', '3', '4', '5', '6', '7', '8', '9'] as $digit)
                        <button wire:click="appendPin('{{ $digit }}')"
                            class="py-3.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-600 text-white font-semibold text-lg rounded-xl transition-all duration-100 active:scale-95">{{ $digit }}</button>
                    @endforeach

                    <button wire:click="clearPin"
                        class="py-3.5 bg-red-900/50 hover:bg-red-800/60 text-red-400 font-semibold text-sm rounded-xl transition-all active:scale-95">CLR</button>

                    <button wire:click="appendPin('0')"
                        class="py-3.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-600 text-white font-semibold text-lg rounded-xl transition-all duration-100 active:scale-95">0</button>

                    <button wire:click="backspacePin"
                        class="py-3.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-600 text-slate-300 font-semibold rounded-xl transition-all active:scale-95 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" />
                        </svg>
                    </button>
                </div>

                <button wire:click="login"
                    class="mt-4 w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl transition-all duration-200 active:scale-95 disabled:opacity-50"
                    :disabled="!$selectedCashierId" wire:loading.attr="disabled">
                    <span wire:loading.remove>Login to POS</span>
                    <span wire:loading>Verifying...</span>
                </button>
            </div>
        </div>

        {{-- Admin link --}}
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-slate-400 transition-colors">
                ← Admin / Manager Login
            </a>
        </div>
    </div>
</div>