<?php

namespace App\Http\Livewire\DataSources;

use App\Models\DataSource;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Arr;
use Livewire\Component;

class Show extends Component
{
    public $dataSource;
    public $x;
    public $y;
    private $elasticClient;

    public function mount(DataSource $source)
    {
        $this->dataSource = $source;

        $this->elasticClient = ClientBuilder::create()
//                ->setHosts(['172.105.150.137'])
            ->setElasticCloudId('N-Covid:dXMtZWFzdC0xLmF3cy5mb3VuZC5pbyRiMTg3ZGZhZTg5Nzc0Yjg0YWUwZmU4ZTM5ZTkzODk3ZSRmN2UxYzFlMmZlYmU0MDIzODcwOWI2NzBjMzUwYjMzZA==')
            ->setBasicAuthentication('elastic', 'Ou9kBKtbMI0iiXKsKkOh2dmY')
            ->build();

        $responses = $this->elasticClient->search([
            'index' => $this->dataSource->index_name,
            'size' => 10000,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ]);

        $data = collect(Arr::pluck($responses['hits']['hits'], '_source'));

        $this->x = $data->where('state', 'RN')->pluck('date')->toArray();
        $this->y = $data->where('state', 'RN')->pluck('newDeaths')->toArray();
    }

    public function render()
    {
        return view('livewire.data-sources.show');
    }
}
