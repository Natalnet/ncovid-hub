<?php

namespace App\Http\Livewire;

use App\Models\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class Dashboard extends Component
{
    public $currentLocation = 'brl:rn';
    public $currentModel;
    public $dateBegin = '2020-04-01';
    public $dateEnd = '2022-02-28';
    public $predictDateBegin = '2020-04-08';
    public $predictDateEnd = '2022-02-28';

    public $locations = [
        'brl' => 'Brasil',
        'brl:ac' => 'Acre',
        'brl:al' => 'Alagoas',
        'brl:ap' => 'Amapá',
        'brl:am' => 'Amazonas',
        'brl:ba' => 'Bahia',
        'brl:ce' => 'Ceará',
        'brl:df' => 'Distrito Federal',
        'brl:es' => 'Espírito Santo',
        'brl:go' => 'Goiás',
        'brl:ma' => 'Maranhão',
        'brl:mt' => 'Mato Grosso',
        'brl:ms' => 'Mato Grosso do Sul',
        'brl:mg' => 'Minas Gerais',
        'brl:pa' => 'Pará',
        'brl:pb' => 'Paraíba',
        'brl:pr' => 'Paraná',
        'brl:pe' => 'Pernambuco',
        'brl:pi' => 'Piauí',
        'brl:rj' => 'Rio de Janeiro',
        'brl:rn' => 'Rio Grande do Norte',
        'brl:rs' => 'Rio Grande do Sul',
        'brl:ro' => 'Rondônia',
        'brl:rr' => 'Roraima',
        'brl:sc' => 'Santa Catarina',
        'brl:sp' => 'São Paulo',
        'brl:se' => 'Sergipe',
        'brl:to' => 'Tocantins',
    ];

    public $dates;
    public $newDeaths;
    public $newCases;
    public $predictedDates;
    public $predictedDeaths;

    public function mount()
    {
        $this->dateEnd = now()->subDays(2)->format('Y-m-d');
        $this->predictDateEnd = now()->addDays(7)->format('Y-m-d');
        $this->useFirstAvailableModel();
        $this->loadData();
    }

    private function availableModels()
    {
        $location = Str::of($this->currentLocation)->after(':')->upper();
        return Model::where('location', $location)->latest()->get()->flatMap(function ($model) {
            return [$model->id => $model->description];
        });
    }

    private function useFirstAvailableModel()
    {
        $this->currentModel = Model::where('location', Str::of($this->currentLocation)->after(':')->upper())->latest()->first();
    }

    public function setSpecificModel($modelId)
    {
        $this->currentModel = Model::findOrFail($modelId);
        $this->loadData();
    }

    protected function loadData() {
        # repo the model was trained for
        $repo = $this->currentModel->metadata['model_configs']['Artificial']['data_configs']['repo']
        # data from where the model was trained for
        $path = $this->currentModel->metadata['model_configs']['Artificial']['data_configs']['path']
        # features the model accept as input
        $inputFeatures = $this->currentModel->metadata['model_configs']['Artificial']['data_configs']['input_features']
        # features the model returns as output. feature returned from $predictionEndpointUrl
        $outputFeatures = $this->currentModel->metadata['model_configs']['Artificial']['data_configs']['output_features']
        # initial date the model was trained for
        $this->dateBegin = $this->currentModel->metadata['model_configs']['Artificial']['data_configs']['date_begin']
        # final date the model was trained for (last trained sample. beyond this date the model gives predictions)
        $this->dateEnd = $this->currentModel->metadata['model_configs']['Artificial']['data_configs']['date_end']
        # days beyond date_end that the model can gives predictions [dateEnd+windowSize]
        $windowSize = $this->currentModel->metadata['model_configs']['Artificial']['data_configs']['window_size']

        # data to plot
        $dataResponse = Http::get('http://ncovid.natalnet.br/datamanager/repo/p971074907/path/'. $this->currentLocation .'/feature/date:'. $outputFeatures .'/begin/'. $this->dateBegin .'/end/'. $this->dateEnd .'/as-json');

        # transforming data from daily to moving average
        # clip first days since theres no moving average for them
        $this->dates = collect($dataResponse->json())->pluck('date')->skip(6)->values()->toArray();
        # calculate moving average 7 days
        $this->newDeaths = collect($dataResponse->json())->pluck('newDeaths')->sliding(7)->map->average()->toArray();

        # data predicted by the model (full historical prediction)
        $predictionEndpointUrl = 'http://ncovid.natalnet.br/predictor/lstm/repo/p971074907/path/'. $this->currentLocation .'/feature/date:'. $inputFeatures .'/begin/'. $this->predictDateBegin .'/end/'. $this->predictDateEnd . '/';

        $predictionResponse = Http::asForm()->post($predictionEndpointUrl, [
            'metadata' => json_encode($this->currentModel->metadata)
        ]);

        # why averaging the output from the model if the model already output data in moving average format?
        $this->predictedDates = collect($predictionResponse->json())->pluck('date')->skip(6)->values()->toArray();
        $this->predictedDeaths = collect($predictionResponse->json())->pluck('prediction')->sliding(7)->map->average()->toArray();

        $data = [
            [
                'x' => $this->dates,
                'y' => $this->newDeaths,
                'mode' => 'lines',
                'line' => [
                    'color' => 'rgb(201,59,59)',
                    'width' => 2
                ],
                'name' => 'New Deaths (7-days moving average)'
            ],
            [
                'x' => $this->predictedDates,
                'y' => $this->predictedDeaths,
                'mode' => 'lines',
                'line' => [
                    'color' => 'rgb(59,196,201)',
                    'width' => 2
                ],
                'name' => 'Predicted New Deaths'
            ]
        ];

        $this->emit('dataUpdated', json_encode($data));
    }

    public function setCurrentLocation($newLocation)
    {
        $this->currentLocation = $newLocation;
        $this->useFirstAvailableModel();

        $this->loadData();
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'availableModels' => $this->availableModels()
        ]);
    }
}
