@props([
    'title' => '',
    'description' => null,
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
    {{-- Sidebar (fixed on mobile, static on desktop) --}}
    <div data-component="Sidebar" class="lg:w-64 lg:flex-shrink-0 lg:h-full">
        <x-sidebar />
    </div>

    {{-- Mobile overlay --}}
    <div
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-gray-600 bg-opacity-75 lg:hidden"
        style="display: none;"
    ></div>

    {{-- Mobile Header (lg:hidden) --}}
    <div data-component="MobileHeader" class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200 flex items-center justify-between px-4 py-3">
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <span class="font-bold text-lg tracking-tight text-gray-900">Gateway Admin</span>
        <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
            <span class="text-white text-xs font-semibold">{{ mb_substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex-1 overflow-y-auto">
        <main class="p-4 lg:p-8 pt-16 lg:pt-8">
            <x-page-header :title="$title" :description="$description" />

            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
