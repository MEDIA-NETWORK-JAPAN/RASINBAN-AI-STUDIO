<?php

use App\Models\MonthlyApiUsage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

new #[Layout('components.admin-layout', ['title' => '利用状況'])]
class extends \Livewire\Volt\Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $month = '';

    public function mount(): void
    {
        if ($this->month === '') {
            $this->month = now()->format('Y-m');
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedMonth(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $search = $this->search;
        $month = $this->month;

        return [
            'usages' => MonthlyApiUsage::with('team')
                ->when($search, fn ($q) => $q->whereHas('team', fn ($t) => $t->where('name', 'ILIKE', "%{$search}%")))
                ->when($month, fn ($q) => $q->where('usage_month', $month))
                ->paginate(50),
        ];
    }
}
?>

<div class="space-y-6">
    {{-- フィルタバー --}}
    <div class="bg-white shadow sm:rounded-lg p-4">
        <div class="flex items-center gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700">対象年月</label>
                <input type="month" wire:model.live="month" value="{{ $month }}" class="ml-2 rounded-md border-gray-300 text-sm">
            </div>
            <div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="拠点名で検索..."
                    class="rounded-md border-gray-300 text-sm w-64"
                />
            </div>
        </div>
    </div>

    {{-- テーブル --}}
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">年月</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">拠点名</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">利用状況</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($usages as $usage)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">
                            {{ $usage->usage_month }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                            {{ $usage->team?->name ?? '(削除済み)' }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ number_format($usage->request_count) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $usages->links() }}
    </div>
</div>
