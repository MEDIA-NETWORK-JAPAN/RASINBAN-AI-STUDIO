<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.admin-layout', ['title' => 'CSV一括登録'])]
class CsvImport extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.csv-import');
    }
}
