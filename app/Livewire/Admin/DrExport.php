<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.admin-layout', ['title' => '災害復旧'])]
class DrExport extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.dr-export');
    }
}
