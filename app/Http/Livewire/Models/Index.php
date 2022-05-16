<?php

namespace App\Http\Livewire\Models;

use App\Models\Model;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Index extends Component
{
    public function deleteModel($modelId)
    {
        $model = Model::findOrFail($modelId);
        Storage::delete('public/models/' . $model->fileName);
        $model->delete();
    }

    public function render()
    {
        return view('livewire.models.index', [
            'models' => Model::all()
        ]);
    }
}
