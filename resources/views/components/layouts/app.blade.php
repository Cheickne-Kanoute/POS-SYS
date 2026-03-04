<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'POS System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full bg-slate-950 font-sans antialiased text-white">
    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col fixed inset-y-0 left-0 z-50">
            {{-- Logo --}}
            <div class="h-16 flex items-center px-6 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">SuperPOS</p>
                        <p class="text-xs text-slate-400">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" @class(['flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors', 'bg-indigo-600 text-white' => request()->routeIs('dashboard'), 'text-slate-400 hover:bg-slate-800 hover:text-white' => !request()->routeIs('dashboard')])>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('users') }}" @class(['flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors', 'bg-indigo-600 text-white' => request()->routeIs('users'), 'text-slate-400 hover:bg-slate-800 hover:text-white' => !request()->routeIs('users')])>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Users
                    </a>
                @endif

                <a href="{{ route('products') }}" @class(['flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors', 'bg-indigo-600 text-white' => request()->routeIs('products'), 'text-slate-400 hover:bg-slate-800 hover:text-white' => !request()->routeIs('products')])>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Products
                </a>

                <a href="{{ route('categories') }}" @class(['flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors', 'bg-indigo-600 text-white' => request()->routeIs('categories'), 'text-slate-400 hover:bg-slate-800 hover:text-white' => !request()->routeIs('categories')])>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Categories
                </a>

                <a href="{{ route('reports') }}" @class(['flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors', 'bg-indigo-600 text-white' => request()->routeIs('reports'), 'text-slate-400 hover:bg-slate-800 hover:text-white' => !request()->routeIs('reports')])>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Reports
                </a>
            </nav>

            {{-- User info & logout --}}
            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-8 h-8 bg-indigo-700 rounded-full flex items-center justify-center text-xs font-bold text-white">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <main class="flex-1 ml-64">
            {{-- Top bar --}}
            <header
                class="h-16 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-8 sticky top-0 z-40">
                <div>
                    <h1 class="text-lg font-semibold text-white">{{ config('app.name', 'SuperPOS') }}</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-400">{{ now()->format('D, d M Y') }}</span>
                    @if(auth()->user()->isAdmin())
                        <span
                            class="px-2 py-1 bg-purple-600/20 text-purple-400 text-xs font-semibold rounded-full border border-purple-600/30">Administrator</span>
                    @elseif(auth()->user()->isManager())
                        <span
                            class="px-2 py-1 bg-blue-600/20 text-blue-400 text-xs font-semibold rounded-full border border-blue-600/30">Manager</span>
                    @endif
                </div>
            </header>

            {{-- Page content --}}
            <div class="p-8">
                {{-- Flash messages --}}
                @if(session('success'))
                    <div
                        class="mb-6 p-4 bg-emerald-900/40 border border-emerald-700 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div
                        class="mb-6 p-4 bg-red-900/40 border border-red-700 rounded-xl text-red-300 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>

</html>