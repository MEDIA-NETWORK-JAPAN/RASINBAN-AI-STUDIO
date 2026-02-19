<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ログイン - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-indigo-100 to-blue-50 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    {{-- Logo / Header --}}
    <div class="text-center mb-8">
        <div
            aria-label="Dify Gateway Logo"
            class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full mb-4"
        >
            <i class="fas fa-network-wired text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-1">Dify Gateway</h1>
        <p class="text-gray-500 text-sm">{{ config('app.name') }}</p>
    </div>

    {{-- Login card --}}
    <div class="bg-white shadow-xl rounded-2xl p-8">

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <ul class="text-sm text-red-800 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @session('status')
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <x-ui.text-input
                    label="メールアドレス"
                    type="email"
                    name="email"
                    :value="old('email')"
                    placeholder="user@example.com"
                    :required="true"
                    autofocus
                    autocomplete="email"
                    ::disabled="loading"
                    class="w-full px-4 py-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
            </div>

            {{-- Password --}}
            <div class="mb-6">
                <x-ui.text-input
                    label="パスワード"
                    type="password"
                    name="password"
                    placeholder="パスワードを入力"
                    :required="true"
                    autocomplete="current-password"
                    ::disabled="loading"
                    class="w-full px-4 py-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
            </div>

            {{-- Submit --}}
            <x-ui.button
                type="submit"
                class="w-full justify-center py-3 px-6"
                icon="fa-sign-in-alt"
                ::disabled="loading"
            >
                <span x-show="!loading">ログイン</span>
                <span x-show="loading" x-cloak>認証中...</span>
            </x-ui.button>
        </form>
    </div>

    <div class="text-center mt-6">
        <p class="text-xs text-gray-500">
            © {{ date('Y') }} Dify Gateway - {{ config('app.name') }}
        </p>
    </div>
</div>

</body>
</html>
