<?php

namespace App\Livewire\Admin;

use App\Models\MonthlyApiUsage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.admin-layout', ['title' => '利用状況'])]
class UsageList extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        $usages = MonthlyApiUsage::with('team')->paginate(50);
        $currentMonth = now()->format('Y-m');

        return view('livewire.admin.usage-list', [
            'usages' => $usages,
            'currentMonth' => $currentMonth,
        ]);
    }
}
