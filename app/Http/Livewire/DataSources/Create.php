<?php

namespace App\Http\Livewire\DataSources;

use App\Models\DataSource;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $sourceName;
    public $sourceSource;
    public $csvFile;
    public $csvUrl;

    public function import()
    {
        $fileHeaders = get_headers($this->csvUrl, true);
        if ($fileHeaders['Content-Length'] > 10 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'csvUrl' => 'The file is too big.'
            ]);
        }

        $fileName = 'data_' . uniqid() . '.csv';

        if(Storage::put($fileName, file_get_contents($this->csvUrl))) {

            $dataSource = new DataSource();
            $dataSource->name = $this->sourceName;
            $dataSource->csv_url = $this->csvUrl;
            $dataSource->csv_path = Storage::path($fileName);
            $dataSource->save();


            $elasticClient = ClientBuilder::create()
//                ->setHosts(['172.105.150.137'])
                ->setElasticCloudId('<cloud-id>')
                ->setBasicAuthentication('<username>', '<password>')
                ->build();
            $params = [
                'index' => $dataSource->index_name,
            ];
            $elasticClient->indices()->create($params);

            $this->redirectRoute('data-sources.parse', ['source' => $dataSource]);

        }

    }

    public function store()
    {
        $this->validate([
            'sourceName' => 'required|string',
            'sourceSource' => 'nullable',
            'csvFile' => 'required|file|max:10240|mimetypes:text/csv,text/plain,application/csv', // 10MB Max
        ]);

        $fileName = 'data_' . uniqid() . '.csv';

        if($this->csvFile->storeAs('data', $fileName)){

            $dataSource = new DataSource();
            $dataSource->name = $this->sourceName;
            $dataSource->csv_url = $this->csvUrl;
            $dataSource->csv_path = Storage::path('data/' . $fileName);
            $dataSource->save();

            $this->redirectRoute('data-sources.parse', ['source' => $dataSource]);
        } else {
            dd("Error");
        }
    }

    public function render()
    {
        return view('livewire.data-sources.create');
    }
}
