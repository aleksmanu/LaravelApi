<?php

namespace App\Modules\Core\Library\ChartLibrary;

class PieChartParser extends ChartParser
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
        foreach ($this->data as $datum) {
            $chart_data[] = [
                $this->value_key    => $datum[$value_key],
                $this->category_key => $datum[$category_key]
            ];
        }
        return $chart_data;
    }
}
