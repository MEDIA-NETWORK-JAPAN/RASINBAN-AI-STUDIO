<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>二段階認証 - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-indigo-100 to-blue-50 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    {{-- Logo / Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full mb-4">
            <i class="fas fa-shield-alt text-white text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">二段階認証</h1>
        <p class="text-gray-500 text-sm">管理者メールに送信された認証コードを入力してください</p>
    </div>

    <div class="bg-white shadow-xl rounded-2xl p-8">
        {{-- Errors --}}
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

        <form method="POST" action="/two-factor-challenge">
            @csrf

            <div class="mb-6">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    認証コード
                </label>
                <input
                    type="text"
                    id="code"
                    name="code"
                    inputmode="numeric"
                    maxlength="6"
                    placeholder="000000"
                    autofocus
                    autocomplete="one-time-code"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-center text-xl tracking-widest font-mono"
                />
            </div>

            <button
                type="submit"
                class="w-full bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition mb-3"
            >
                認証する
            </button>
        </form>

        {{-- Resend + Logout --}}
        <div class="flex gap-3 mt-2">
            <form method="POST" action="/two-factor-challenge/resend" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full py-2 px-4 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                    再送信
                </button>
            </form>

            <form method="POST" action="/logout" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full py-2 px-4 border border-red-200 rounded-lg text-sm text-red-600 hover:bg-red-50 transition">
                    ログアウト
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
