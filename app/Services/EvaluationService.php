<?php

namespace App\Services;

use Carbon\Carbon;

class EvaluationService
{
    /**
     * @param array $actualData actual data available
     * @param array $forecastData data of predictions
     * @param Carbon $initialDate date in which the model was made available
     * @return array
     */
    public static function mape(array $actualData, array $forecastData, Carbon $initialDate)
    {
        /*
         * Create an array in the for of [date => [actual, forecast]]
         * For future days, the form will be [date => forecast]
         */
        $evalCombo = array_merge_recursive($actualData, $forecastData);
        $ape = array_map(function ($key, $value) use ($initialDate) {
            // remove dates with only forecast (future dates)
            if (! is_array($value)) {
                return null;
            }

            // calculate the APE for dates after the model was trained
            $tmpDate = Carbon::createFromDate($key);
            if ($tmpDate->greaterThan($initialDate)) {
                return abs(($value[0] - $value[1])/max($value[0], 0.1));
            }

            // remove previous dates
            return null;
        }, array_keys($evalCombo), array_values($evalCombo));
        $ape = array_combine(array_keys($evalCombo), $ape);
        $ape = array_filter($ape, function ($value) {
            return $value !== null;
        });

        $mape = [];
        $apeSum = 0;
        $apeCount = 0;
        foreach ($ape as $key => $value) {
            $apeSum += $value;
            $apeCount++;
            $mape[$key] = $apeSum / $apeCount;
        }

        return $mape;
    }
}
