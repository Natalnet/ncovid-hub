<?php

namespace App\Http\Livewire\DataSources;

use App\Jobs\FetchData;
use App\Models\DataSource;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Parse extends Component
{
    public $dataSource;
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

    public function mount(DataSource $source)
    {
        $this->dataSource = $source;
        if (($handle = fopen($this->dataSource->csv_path, "r")) !== FALSE) {
            if (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $this->csvColumns = $data;
            }
            fclose($handle);
        }
    }

    public function addMapping()
    {
        $this->columnMappings[$this->state['mapped']] = $this->state['original'];
        $this->reset('state');
    }

    public function fetchData()
    {
        FetchData::dispatch($this->dataSource);
        $this->redirectRoute('data-sources.index');
    }

    public function render()
    {
        return view('livewire.data-sources.parse');
    }
}
