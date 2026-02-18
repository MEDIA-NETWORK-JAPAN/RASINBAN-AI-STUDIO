<?php

namespace App\Livewire\Admin;

use App\Models\DifyApp;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.admin-layout', ['title' => 'Difyアプリ一覧'])]
class DifyAppList extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        $apps = DifyApp::paginate(50);

        return view('livewire.admin.dify-app-list', [
            'apps' => $apps,
        ]);
    }
}
