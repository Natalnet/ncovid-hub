<?php

namespace App\Http\Controllers;

use App\Services\DataStatsService;
use Illuminate\Support\Str;

class DataStatsController extends Controller
{
    public function weeklyCumulativeComparison($repo, $path, $features, $date)
    {
        $dataStats = new DataStatsService($repo, $path);
        $weeklyData = $dataStats->weeklyCumulativeComparison(Str::of($features)->after('date:'), $date);

        return [
            'periodo_semanal_um_inicio' => $weeklyData['first']['start']->toDateString(),
            'periodo_semanal_um_fim' => $weeklyData['first']['end']->toDateString(),
            'periodo_semanal_dois_inicio' => $weeklyData['last']['start']->toDateString(),
            'periodo_semanal_dois_fim' => $weeklyData['last']['end']->toDateString(),
            'acumulado_semana_um' => $weeklyData['first']['cumulative'],
            'acumulado_semana_dois' => $weeklyData['last']['cumulative']
        ];
    }
}
