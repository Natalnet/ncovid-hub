<?php

namespace App\Http\Livewire\Importer;

use Elasticsearch\ClientBuilder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Import extends Component
{
    public $csvUrl;
    public $supportedColumns = [
        'date',
        'state',
        'newDeaths'
    ];
    public $csvColumns = [];
    public $columnMappings = [];
    public $state = [
        'original' => '',
        'mapped' => ''
    ];

    public function import()
    {
        $fileHeaders = get_headers($this->csvUrl, true);
        if ($fileHeaders['Content-Length'] > 10 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'csvUrl' => 'The file is too big.'
            ]);
        }
        $file = Storage::put('data.csv', file_get_contents($this->csvUrl));

        if (($handle = fopen(Storage::path('data.csv'), "r")) !== FALSE) {
            if (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $this->csvColumns = $data;
            }
            fclose($handle);
        }

//        $client = ClientBuilder::create()->build();
    }

    public function addMapping()
    {
        $this->columnMappings[$this->state['mapped']] = $this->state['original'];
        $this->reset('state');
    }

    public function render()
    {
        return view('livewire.importer.import');
    }
}
