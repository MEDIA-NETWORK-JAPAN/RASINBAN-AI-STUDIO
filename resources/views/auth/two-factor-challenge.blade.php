<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>二段階認証 - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .otp-input {
            font-size: 2rem;
            letter-spacing: 0.5rem;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-100 to-blue-50 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    {{-- Logo / Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full mb-4">
            <i class="fas fa-shield-alt text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">二段階認証</h1>
        <p class="text-gray-600 text-sm">
            {{ $adminName }} 宛に6桁の認証コードを送信しました
        </p>
    </div>

    <div class="bg-white shadow-xl rounded-2xl p-8" x-data="{ loading: false }">
        {{-- Success status --}}
        @session('status')
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm text-green-800">{{ $value }}</p>
            </div>
        @endsession

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

        <form method="POST" action="/two-factor-challenge" @submit="loading = true">
            @csrf

            <div class="mb-6">
                <x-ui.text-input
                    label="認証コード"
                    type="text"
                    name="code"
                    inputmode="numeric"
                    maxlength="6"
                    placeholder="000000"
                    autofocus
                    autocomplete="one-time-code"
                    ::disabled="loading"
                    class="otp-input w-full px-4 py-4 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
                <p class="text-xs text-gray-500 mt-2">6桁の数字を入力してください</p>
            </div>

            <x-ui.button
                type="submit"
                class="w-full justify-center py-3 px-6 mb-4"
                icon="fa-lock"
                ::disabled="loading"
            >
                <span x-show="!loading">認証する</span>
                <span x-show="loading" x-cloak>検証中...</span>
            </x-ui.button>
        </form>

        {{-- Resend + Logout --}}
        <div class="flex gap-3">
            <form method="POST" action="/two-factor-challenge/resend" class="flex-1">
                @csrf
                <x-ui.button type="submit" variant="secondary" class="w-full justify-center py-2.5 px-4" icon="fa-envelope">
                    コードを再送信
                </x-ui.button>
            </form>

            <form method="POST" action="/logout" class="flex-1">
                @csrf
                <x-ui.button type="submit" variant="secondary" class="w-full justify-center py-2.5 px-4" icon="fa-sign-out-alt">
                    ログアウト
                </x-ui.button>
            </form>
        </div>

        {{-- 注記 --}}
        <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <i class="fas fa-clock text-gray-400 mt-0.5 flex-shrink-0"></i>
                <p>このコードは <strong>10分間</strong> 有効です。</p>
            </div>
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <i class="fas fa-shield-alt text-gray-400 mt-0.5 flex-shrink-0"></i>
                <p>コードを他人に教えないでください。</p>
            </div>
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <i class="fas fa-envelope text-gray-400 mt-0.5 flex-shrink-0"></i>
                <p>コードは <strong>ユーザーID=1の管理者</strong> に送信されています。受信していない場合は管理者にお問い合わせください。</p>
            </div>
        </div>

        {{-- 試行回数表示 --}}
        @if ($attempts > 0)
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">試行回数:</span>
                    <span class="font-semibold {{ $attempts >= $maxAttempts ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $attempts }} / {{ $maxAttempts }}
                    </span>
                </div>
                <div class="mt-2">
                    <x-ui.progress-bar :percentage="min(100, round($attempts / $maxAttempts * 100))" />
                </div>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="text-center mt-6">
        <p class="text-xs text-gray-500">
            © {{ date('Y') }} Dify Gateway - {{ config('app.name') }}
        </p>
    </div>
</div>

</body>
</html>
