<?php

namespace App\Modules\Core\Library\ChartLibrary;

class LineChartParser extends ChartParser
{

    /**
     * @param string $category_key
     * @param string $value_key
     * @return array
     */
    public function get($category_key = '', $value_key = ''): array
    {

        $category_key = $category_key ?: $this->category_key;
        $value_key    = $value_key ?: $this->value_key;

        $chart_data = [];
        foreach ($this->data as $k => $series) {
            $chart_series = [];
            foreach ($series as $row) {
                $chart_series[] = [
                    $this->value_key    => $row[$value_key],
                    $this->category_key => $row[$category_key]
                ];
            }
            $chart_data[] = [
                $this->category_key => $k,
                $this->series_key   => $chart_series
            ];
        }
        $this->data = []; //Reset the data variable
        return $chart_data;
    }
}
