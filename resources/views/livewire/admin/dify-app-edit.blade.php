<?php

use App\Models\DifyApp;
use Livewire\Attributes\Layout;

new #[Layout('components.admin-layout', ['title' => 'アプリ編集'])]
class extends \Livewire\Volt\Component
{
    public DifyApp $difyApp;

    public function mount(DifyApp $difyApp): void
    {
        $this->difyApp = $difyApp;
    }

    public function with(): array
    {
        return [
            'app' => $this->difyApp,
        ];
    }
}
?>

<div class="space-y-6">
    {{-- 基本情報 --}}
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">アプリ編集</h3>

            <dl class="mt-4 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">アプリ名</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $app->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Slug</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $app->slug }}</dd>
                </div>
                @if ($app->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">説明</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->description }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">ステータス</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if ($app->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">有効</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">無効</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Dify接続情報 --}}
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Dify接続情報</h3>

            <dl class="mt-4 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">エンドポイントURL</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $app->base_url }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">APIキー</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-900">••••••••••••••••</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
