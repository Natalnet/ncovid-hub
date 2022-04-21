<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DataStatsService
{
    public $dataHost;
    public $repo;
    public $path;
    public $data;

    public function __construct($repo, $path)
    {
        $this->dataHost = config('datamanager.host');
        $this->repo = $repo;
        $this->path = $path;
    }

    public function weeklyCumulativeComparison(string $feature, $endOfPeriod, $weekDifference = 4)
    {
        $endOfFullPeriod = Carbon::createFromFormat('Y-m-d', $endOfPeriod)->startOfDay();
        $startOfFullPeriod = $endOfFullPeriod->copy()->subWeeks($weekDifference + 2)->startOfDay(); // 15 days margin at the start

        $this->fetchData($feature, $startOfFullPeriod, $endOfFullPeriod);

        $periods = $this->weeklyPeriodsThresholds($endOfFullPeriod, $weekDifference = 4);

        $periods['first']['cumulative'] = $this->featureCumulativeForPeriod($feature, $periods['first']['start'], $periods['first']['end']);
        $periods['last']['cumulative'] = $this->featureCumulativeForPeriod($feature, $periods['last']['start'], $periods['last']['end']);

        return $periods;
    }

    protected function fetchData($feature, Carbon $startOfPeriod, Carbon $endOfPeriod)
    {
        $dataResponse = Http::get($this->prepareUrl($feature, $startOfPeriod, $endOfPeriod));
        $dates = collect($dataResponse->json())->pluck('date')->values()->toArray();
        $featureValues = collect($dataResponse->json())->pluck($feature)->values()->toArray();

        $this->data = collect(array_map(function ($date, $featureValue) use ($feature) {
            return [
                'date' => Carbon::createFromFormat('Y-m-d', $date)->startOfDay(),
                $feature => $featureValue,
            ];
        }, $dates, $featureValues));

    }

    protected function prepareUrl($feature, Carbon $startOfPeriod, Carbon $endOfPeriod): string
    {
        return Str::replaceArray('?', [
            $this->dataHost,
            $this->repo,
            $this->path,
            $feature,
            $startOfPeriod->format('Y-m-d'),
            $endOfPeriod->format('Y-m-d'),
        ], '?/repo/?/path/?/feature/date:?/begin/?/end/?/as-json');
    }

    protected function weeklyPeriodsThresholds(Carbon $endOfFullPeriod, int $weekDifference): array
    {
        return [
            'first' => [
                'start' => $endOfFullPeriod->copy()->subWeeks($weekDifference)->subDays(6),
                'end' => $endOfFullPeriod->copy()->subWeeks($weekDifference)
            ],
            'last' => [
                'start' => $endOfFullPeriod->copy()->subDays(6),
                'end' => $endOfFullPeriod->copy()
            ]
        ];
    }

    protected function featureCumulativeForPeriod($feature, Carbon $start, Carbon $end)
    {
        if ($this->data == null)
            return null;

        return $this->data->filter(function ($dataValue) use ($start, $end) {
            return $start->lessThanOrEqualTo($dataValue['date']) && $end->greaterThanOrEqualTo($dataValue['date']);
        })->sum($feature);
    }
}
