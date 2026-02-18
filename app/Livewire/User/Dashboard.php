<?php

namespace App\Livewire\User;

use App\Models\MonthlyApiUsage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.admin-layout', ['title' => 'ユーザーダッシュボード'])]
class Dashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        $userId = auth()->id();
        $currentMonth = now()->format('Y-m');

        $usages = MonthlyApiUsage::where('user_id', $userId)
            ->where('usage_month', $currentMonth)
            ->get();

        return view('livewire.user.dashboard', [
            'usages' => $usages,
        ]);
    }
}
