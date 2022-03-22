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
    public $metadata;

    public function store()
    {
        $this->validate([
            'location' => 'required|string|size:2',
            'description' => 'required|string|max:255',
            'file' => 'required|file|max:20480|mimes:hdf', // 20MB Max
            'metadata' => 'required|json'
        ]);

        $fileId = Str::uuid();
        $fileName = $fileId . '.h5';
        $metadata = json_decode($this->metadata, true);
        $metadata['folder_configs'] = [
            'model_path_remote' => 'http://ncovid.natalnet.br/storage/models/'
        ];
        $metadata['model_configs']['model_id'] = $fileId;

        if($this->file->storeAs('public/models', $fileName)){
            $model = Model::create(
                [
                    'location' => $this->location,
                    'description' => $this->description,
                    'file_name' => $fileName,
                    'metadata' => $metadata
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
