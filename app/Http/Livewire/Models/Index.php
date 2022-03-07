<?php

namespace App\Http\Livewire\Models;

use App\Models\Model;
use Livewire\Component;

class Index extends Component
{
    public $models;

    public function mount()
    {
        $this->models = Model::all();
    }

    public function render()
    {
        return view('livewire.models.index');
    }
}
