<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Terminal – {{ config('app.name', 'SuperPOS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- QZ Tray Dependencies (Local to avoid Adblockers) -->
    <script src="{{ asset('js/sha256.min.js') }}"></script>
    <script src="{{ asset('js/qz-tray.min.js') }}"></script>
</head>

<body class="h-full bg-slate-950 font-sans antialiased text-white overflow-hidden">
    {{-- POS Top Bar --}}
    <div
        class="h-14 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-6 fixed top-0 left-0 right-0 z-50">
        <div class="flex items-center gap-4">
            <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="font-bold text-white">SuperPOS <span
                    class="text-slate-500 font-normal text-sm">Terminal</span></span>
        </div>
        <div class="flex items-center gap-6 text-sm">
            <span class="text-slate-400">
                <span class="font-medium text-white">{{ auth()->user()->name }}</span> – Cashier
            </span>
            <span class="text-slate-500">{{ now()->format('H:i · D d M') }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg text-xs font-medium transition-colors">
                    Sign Out
                </button>
            </form>
        </div>
    </div>

    {{-- Main content --}}
    <div class="pt-14 h-full">
        {{ $slot }}
    </div>

    @livewireScripts
</body>

</html>