<?php

use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

new #[Layout('components.admin-layout', ['title' => '拠点一覧'])]
class extends \Livewire\Volt\Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'teams' => Team::when(
                $this->search,
                fn ($q) => $q->where('name', 'ILIKE', "%{$this->search}%")
            )->paginate(50),
        ];
    }
}
?>

<div class="space-y-6">
    {{-- 検索バー --}}
    <div class="bg-white shadow sm:rounded-lg p-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="拠点名で検索..."
            class="rounded-md border-gray-300 text-sm w-full sm:w-64"
        />
    </div>

    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">拠点名</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">メンバー数</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">作成日</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($teams as $team)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                            <a href="{{ route('admin.teams.edit', $team) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">
                                {{ $team->name }}
                            </a>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ $team->users()->count() }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ $team->created_at->format('Y-m-d') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $teams->links() }}
    </div>
</div>
