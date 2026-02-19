<?php

use App\Models\Team;
use Livewire\Attributes\Layout;

new #[Layout('components.admin-layout', ['title' => '拠点編集'])]
class extends \Livewire\Volt\Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        $this->team = $team;
    }

    public function with(): array
    {
        $members = $this->team->users()->get();
        $apiKeys = $this->team->users()->with('apiKeys')->get()->flatMap(fn ($user) => $user->apiKeys);

        return [
            'team' => $this->team,
            'members' => $members,
            'apiKeys' => $apiKeys,
        ];
    }
}
?>

<div class="space-y-6">
    {{-- 基本情報 --}}
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">拠点編集</h3>
            <p class="mt-1 text-sm text-gray-500">{{ $team->name }}</p>
        </div>
    </div>

    {{-- メンバー一覧 --}}
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">所属ユーザー</h3>
            <div class="mt-4 space-y-2">
                @foreach ($members as $member)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-900">{{ $member->name }}</span>
                        <span class="text-sm text-gray-500">{{ $member->email }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- APIキー一覧 --}}
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">APIキー</h3>
            <div class="mt-4 space-y-2">
                @foreach ($apiKeys as $apiKey)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm font-mono text-gray-900">{{ substr($apiKey->key_hash, 0, 8) }}••••••••</span>
                        <span class="text-sm text-gray-500">
                            {{ $apiKey->last_used_at ? $apiKey->last_used_at->format('Y-m-d H:i') : '未使用' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
