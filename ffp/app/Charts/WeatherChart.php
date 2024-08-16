<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class WeatherChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function buildWeatherChart($actualData, $predictData, $labels, $title): \ArielMejiaDev\LarapexCharts\LineChart
    {
        // Round the data to one decimal place
        $actualData = array_map(function ($value) {
            return round($value, 1);
        }, $actualData);

        $predictData = array_map(function ($value) {
            return round($value, 1);
        }, $predictData);

        // Format the date labels
        $formattedLabels = array_map(function ($label) {
            $date = new \DateTime($label);
            return $date->format('d M Y'); // Format as 'dd Mmm yyyy'
        }, $labels);

        return $this->chart->lineChart()
            ->setTitle($title)
            ->setLabels($formattedLabels)
            ->addData('Actual', $actualData)
            ->addData('Predicted', $predictData)
            ->setColors(['#FF4560', '#00E396'])
            ->setDataset([
                [
                    'name' => 'Actual',
                    'data' => $actualData,
                    'type' => 'line',
                    'color' => '#FF4560',
                ],
                [
                    'name' => 'Predicted',
                    'data' => $predictData,
                    'type' => 'line',
                    'color' => '#00E396',
                ],
            ])
            ->setStroke(2);
    }
}
