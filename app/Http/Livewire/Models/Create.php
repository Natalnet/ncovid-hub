<?php

namespace App\Http\Livewire\Models;

use App\Models\Model;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $location;
    public $description;
    public $file;

    public function store()
    {
        $this->validate([
            'location' => 'required|string|size:2',
            'description' => 'required|string|max:255',
            'file' => 'required|file|max:20480|mimes:hdf', // 20MB Max
        ]);

        $fileName = $this->file->getClientOriginalName();

        if($this->file->storeAs('public/models', $fileName)){
            $model = Model::updateOrCreate(
                ['location' => $this->location],
                [
                    'description' => $this->description,
                    'file_name' => $fileName
                ]
            );

            $this->redirectRoute('models.index');
        } else {
            $this->addError('file', 'There was an error uploading your file.');
        }
    }
    public function render()
    {
        return view('livewire.models.create');
    }
}
