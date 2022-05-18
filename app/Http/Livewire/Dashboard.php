<?php

namespace App\Http\Livewire;

use App\Models\Model;
use App\Services\DataStatsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class Dashboard extends Component
{
    public $currentLocation = 'brl';
    public $currentModels = [];
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

    public $timeseriesChartData;
    public $weeklyCumulativeComparisonChartData;

    public function mount()
    {
        $this->dateEnd = now()->format('Y-m-d');
        # TODO: addDays argument should be value output_window_size from metadata
        $this->predictDateEnd = now()->addDays(7)->format('Y-m-d');
        $this->useFirstAvailableModel();
        $this->loadData();
    }

    private function mapLocation($location)
    {
        if ($location !== 'brl') {
            return Str::of($location)->after(':')->upper();
        } else {
            return 'BR';
        }
    }

    private function availableModels()
    {
        return Model::where('location', $this->mapLocation($this->currentLocation))->latest()->get()->flatMap(function ($model) {
            return [$model->id => $model->description];
        });
    }

    private function useFirstAvailableModel()
    {
        $model = Model::where('location', $this->mapLocation($this->currentLocation))->latest()->first();
        $this->currentModels[$model->id] = $model;
    }

    public function toggleSpecificModel($modelId)
    {
        if (isset($this->currentModels[$modelId])) {
            unset($this->currentModels[$modelId]);
        } else {
            $this->currentModels[$modelId] = Model::findOrFail($modelId);
        }
        $this->loadData();
    }

    protected function loadData() {

        foreach ($this->currentModels as $currentModel) {
            $metadata = $currentModel['metadata'];
            # repo the model was trained for
            $repo = $metadata['model_configs']['Artificial']['data_configs']['repo'];
            # data from where the model was trained for
            $path = $metadata['model_configs']['Artificial']['data_configs']['path'];
            # features the model accept as input
            $inputFeatures = $metadata['model_configs']['Artificial']['data_configs']['input_features'];
            # features the model returns as output. feature returned from $predictionEndpointUrl
            $outputFeatures = $metadata['model_configs']['Artificial']['data_configs']['output_features'];

            # initial date the model was trained for
            $dateBegin = $metadata['model_configs']['Artificial']['data_configs']['date_begin'];
            # final date the model was trained for (last trained sample. beyond this date the model gives predictions)
            $dateEnd = $metadata['model_configs']['Artificial']['data_configs']['date_end'];
            # days beyond date_end that the model can gives predictions [dateEnd+windowSize]
            $windowSize = $metadata['model_configs']['Artificial']['data_configs']['window_size'];
            # TODO get mavg_windowSize from metadata
            $mavg_windowSize = 7;

            # data to plot
            $dataResponse = Http::get('http://ncovid.natalnet.br/datamanager/repo/p971074907/path/'. $this->currentLocation .'/features/date:'. $outputFeatures. '/window-size/' . $mavg_windowSize . '/begin/'. $this->dateBegin .'/end/'. $this->dateEnd .'/as-json');

            # transforming data from daily to moving average
            # clip first days since theres no moving average for them
            $this->dates = collect($dataResponse->json())->pluck('date')->toArray();

            # calculate moving average 7 days
            $this->newDeaths = collect($dataResponse->json())->pluck($outputFeatures.'_mavg')->toArray();

            $this->timeseriesChartData = [
                [
                    'x' => $this->dates,
                    'y' => $this->newDeaths,
                    'mode' => 'lines',
                    'line' => [
                        'color' => 'rgb(201,59,59)',
                        'width' => 2
                    ],
                    'name' => 'Deaths (7-days moving average)'
                ]
            ];

            # data predicted by the model (full historical prediction)
            $predictionEndpointUrl = 'http://ncovid.natalnet.br/predictortest/lstm/repo/'.$repo.'/path/'. $this->currentLocation .'/feature/date:'. $inputFeatures .'/begin/'. $this->dateBegin .'/end/'. $this->predictDateEnd . '/';

            $predictionResponse = Http::asForm()->post($predictionEndpointUrl, [
                'metadata' => json_encode($metadata)
            ]);

            # why averaging the output from the model if the model already output data in moving average format?
            $this->predictedDates = collect($predictionResponse->json())->pluck('date')->values()->toArray();
            $this->predictedDeaths = collect($predictionResponse->json())->pluck('prediction')->toArray();

            $this->timeseriesChartData[] = [
                'x' => $this->predictedDates,
                'y' => $this->predictedDeaths,
                'mode' => 'lines',
                'line' => [
                    'color' => $this->randomColor(),
                    'width' => 2
                ],
                'name' => 'Predicted Deaths - ' . $currentModel['description']
            ];
        }

        $dataStats = new DataStatsService('p971074907', $this->currentLocation);
        $weeklyCumulativeComparisonData = $dataStats->weeklyCumulativeComparison('newDeaths', now()->subDay()->toDateString());

        $this->weeklyCumulativeComparisonChartData = [
            [
                'type' => 'bar',
                'x' => [$weeklyCumulativeComparisonData['first']['cumulative'], $weeklyCumulativeComparisonData['last']['cumulative']],
                'y' => [
                    $weeklyCumulativeComparisonData['first']['start']->toFormattedDateString() . '<br>to ' . $weeklyCumulativeComparisonData['first']['end']->toFormattedDateString(),
                    $weeklyCumulativeComparisonData['last']['start']->toFormattedDateString() . '<br>to ' . $weeklyCumulativeComparisonData['last']['end']->toFormattedDateString()
                ],
                'marker' => [
                    'color' => 'rgba(202,66,59,1)',
                ],
                'name' => 'Cumulative deaths in a week',
                'orientation' => 'h'
            ]
        ];

        $this->emit('dataUpdated', json_encode([$this->timeseriesChartData, $this->weeklyCumulativeComparisonChartData]));
    }

    private function randomColor()
    {
        // format rgb(59,196,201)
        foreach(array('r', 'g', 'b') as $color){
            //Generate a random number between 0 and 255.
            $rgbColor[$color] = mt_rand(0, 255);
        }
        return 'rgb(' . implode(",", $rgbColor) . ')';
    }

    public function setCurrentLocation($newLocation)
    {
        $this->currentLocation = $newLocation;
        $this->currentModels = [];
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
