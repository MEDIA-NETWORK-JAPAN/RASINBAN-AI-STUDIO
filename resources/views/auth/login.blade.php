<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ログイン - {{ config('app.name') }}</title>
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

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    メールアドレス
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="user@example.com"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm"
                />
            </div>

            {{-- Password --}}
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    パスワード
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="パスワードを入力"
                    required
                    autocomplete="current-password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm"
                />
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition flex items-center justify-center gap-2"
            >
                <i class="fas fa-sign-in-alt"></i>
                ログイン
            </button>
        </form>
    </div>
</div>

</body>
</html>
