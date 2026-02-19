@props([
    'title' => '',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title }} - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    {{-- Mobile Header --}}
    <div class="lg:hidden flex items-center justify-between bg-white border-b border-gray-200 px-4 py-3 fixed top-0 left-0 right-0 z-30">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <span class="font-bold text-lg text-gray-900">Gateway</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
        </div>
    </div>

    {{-- Mobile Overlay --}}
    <div
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
        x-cloak
    ></div>

    {{-- Sidebar --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col"
    >
        {{-- Logo --}}
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <i class="fas fa-network-wired text-xl text-indigo-600"></i>
                <span class="font-bold text-xl tracking-tight text-gray-900">Dify <span class="text-indigo-600">Gateway</span></span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Menu</p>
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg bg-indigo-50 text-indigo-700 group">
                <i class="fas fa-chart-bar w-5 text-center"></i>
                ダッシュボード
            </a>
        </nav>

        {{-- User Info --}}
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm font-semibold">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->currentTeam?->name ?? '-' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-gray-600" title="ログアウト">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">

        {{-- Page Header --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between pt-16 lg:pt-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800">マイダッシュボード</h1>
                <p class="text-xs text-gray-500 mt-1">{{ Auth::user()->currentTeam?->name }} / {{ Auth::user()->plan?->name ?? 'プランなし' }}</p>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ now()->isoFormat('YYYY年M月') }}</span>
            </div>
        </header>

        {{-- Slot --}}
        <div class="flex-1 overflow-y-auto p-4 lg:p-8">
            <div class="max-w-4xl mx-auto">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

@livewireScripts
</body>
</html>
