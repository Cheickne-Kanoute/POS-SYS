<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-2xl mb-4 shadow-2xl shadow-indigo-900/50">
                <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">SuperPOS</h1>
            <p class="text-slate-400 text-sm mt-1">Backoffice Login</p>
        </div>

        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8 shadow-2xl">
            <form wire:submit="login" class="space-y-5">
                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                    <input wire:model="email" id="email" type="email" autocomplete="email" placeholder="admin@pos.com"
                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    @error('email') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                    <input wire:model="password" id="password" type="password" autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    @error('password') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Remember --}}
                <div class="flex items-center">
                    <input wire:model="remember" id="remember" type="checkbox"
                        class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                    <label for="remember" class="ml-2 text-sm text-slate-400">Remember me</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all duration-200 hover:shadow-lg hover:shadow-indigo-900/50 active:scale-95"
                    wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                    <span wire:loading.remove>Sign In</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Signing in...
                    </span>
                </button>
            </form>

            {{-- Cashier link --}}
            <div class="mt-6 pt-6 border-t border-slate-700/50 text-center">
                <p class="text-sm text-slate-500">Are you a cashier?</p>
                <a href="{{ route('cashier.login') }}"
                    class="inline-flex items-center gap-1 mt-1 text-sm text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Login with PIN
                </a>
            </div>
        </div>
    </div>
</div>