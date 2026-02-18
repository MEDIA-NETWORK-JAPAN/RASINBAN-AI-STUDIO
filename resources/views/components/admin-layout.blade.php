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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-100">

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
    {{-- Sidebar (fixed on mobile, static on desktop) --}}
    <div data-component="Sidebar" class="lg:flex-shrink-0">
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
    <div class="lg:hidden fixed top-0 left-0 right-0 z-40 MobileHeader bg-white shadow-sm flex items-center justify-between px-4 py-3">
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="text-gray-500">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <span class="text-sm font-semibold text-gray-900">{{ config('app.name') }}</span>
        <div></div>
    </div>

    {{-- Main content --}}
    <div class="flex-1 overflow-y-auto">
        <main class="p-6 pt-16 lg:pt-6">
            <x-page-header :title="$title" :description="$description" />

            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
