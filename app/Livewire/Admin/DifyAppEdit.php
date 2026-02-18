<?php

namespace App\Livewire\Admin;

use App\Models\DifyApp;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.admin-layout', ['title' => 'アプリ編集'])]
class DifyAppEdit extends Component
{
    public DifyApp $difyApp;

    public function mount(DifyApp $difyApp): void
    {
        $this->difyApp = $difyApp;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.dify-app-edit', [
            'app' => $this->difyApp,
        ]);
    }
}
