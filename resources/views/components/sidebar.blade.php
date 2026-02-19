@props([])

@php
    $currentRoute = request()->route()?->getName();
@endphp

<div
    class="fixed inset-y-0 left-0 z-40 w-64 h-full bg-white border-r border-gray-200 transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
>
    {{-- Logo area --}}
    <div class="h-16 flex items-center px-6 border-b border-gray-100">
        <div class="flex items-center gap-2 text-indigo-600">
            <i class="fas fa-network-wired text-xl"></i>
            <span class="font-bold text-xl tracking-tight text-gray-900">Gateway Admin</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
        @if (isset($navigation))
            {{ $navigation }}
        @else
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Main</p>
            <a href="{{ route('admin.dashboard') }}" wire:navigate
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $currentRoute === 'admin.dashboard' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-chart-line w-5 text-center {{ $currentRoute === 'admin.dashboard' ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                ダッシュボード
            </a>

            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2">Management</p>
            <a href="{{ route('admin.teams') }}" wire:navigate
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with((string) $currentRoute, 'admin.teams') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-building w-5 text-center {{ str_starts_with((string) $currentRoute, 'admin.teams') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                拠点・ユーザー管理
            </a>
            <a href="{{ route('admin.apps') }}" wire:navigate
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with((string) $currentRoute, 'admin.apps') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-robot w-5 text-center {{ str_starts_with((string) $currentRoute, 'admin.apps') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                Difyアプリ管理
            </a>
            <a href="{{ route('admin.usages') }}" wire:navigate
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with((string) $currentRoute, 'admin.usages') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-file-invoice-dollar w-5 text-center {{ str_starts_with((string) $currentRoute, 'admin.usages') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                利用状況・制限
            </a>

            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2">System</p>
            <a href="{{ route('admin.dr.export') }}" wire:navigate
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with((string) $currentRoute, 'admin.dr.export') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-server w-5 text-center {{ str_starts_with((string) $currentRoute, 'admin.dr.export') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
                災害復旧 (DR)
            </a>
        @endif
    </nav>

    @auth
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm font-semibold">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-600" title="ログアウト">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    @endauth
</div>
