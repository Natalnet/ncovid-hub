<?php

namespace App\Http\Livewire\Models;

use Livewire\Component;
use App\Models\Model;

class Show extends Component
{
    public $model;

    public function mount(Model $model)
    {
        $this->model = $model;
    }

    public function render()
    {
        return view('livewire.models.show');
    }
}
