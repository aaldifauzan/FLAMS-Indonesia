<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class MapsChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function buildFWIChart($labels, $ffmc, $dmc, $dc, $isi, $bui, $fwi)
    {
        return $this->chart->lineChart()
            ->setTitle('FWI Data')
            ->setLabels($labels)
            ->addLine('FFMC', $ffmc)
            ->addLine('DMC', $dmc)
            ->addLine('DC', $dc)
            ->addLine('ISI', $isi)
            ->addLine('BUI', $bui)
            ->addLine('FWI', $fwi);
    }
}
