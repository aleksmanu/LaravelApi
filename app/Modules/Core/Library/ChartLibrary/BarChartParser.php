<?php

namespace App\Modules\Core\Library\ChartLibrary;

class BarChartParser extends ChartParser
{

    const STANDARD = 'standard';
    const STACKED  = 'stacked';
    const GROUPED  = 'grouped';
    const NEGATIVE = 'negative';

    /**
     * @var string
     */
    protected $series_type = 'standard';

    /**
     * @param $series_type
     * @return $this
     */
    public function setSeriesType($series_type)
    {
        $this->series_type = $series_type;
        return $this;
    }

    /**
     * @param string $category_key
     * @param string $value_key
     * @return array
     * @throws \Exception
     */
    public function get($category_key = '', $value_key = ''): array
    {

        $category_key = $category_key ?: $this->category_key;
        $value_key    = $value_key ?: $this->value_key;

        if ($this->series_type === self::STACKED || $this->series_type === self::GROUPED) {
            return $this->setStacked($category_key, $value_key);
        } elseif ($this->series_type === self::NEGATIVE) {
            return $this->setNegative($category_key, $value_key);
        } else {
            return $this->setStandard($category_key, $value_key);
        }
    }

    /**
     * @param $category_key
     * @param $value_key
     * @return array
     */
    private function setStandard($category_key, $value_key): array
    {

        $chart_data = [];
        foreach ($this->data as $datum) {
            $chart_data[] = [
                $this->value_key    => $datum[$value_key],
                $this->category_key => $datum[$category_key]
            ];
        }
        $this->data = [];
        return $chart_data;
    }

    /**
     * @param $category_key
     * @param $value_key
     * @return array
     */
    private function setStacked($category_key, $value_key): array
    {

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

    /**
     * @param $category_key
     * @param $value_key
     * @return array
     * @throws \Exception
     */
    private function setNegative($category_key, $value_key): array
    {

        $chart_data = [];
        foreach ($this->data as $k => $series) {
            if (count($series) !== 2) {
                throw new \Exception('Negative series must only have two values');
            }

            $chart_series = [];
            foreach ($series as $index => $row) {
                if ($index === 0) {
                    $row[$value_key] = 0 - floatval($row[$value_key]);
                }
                $chart_series[] = [
                    $this->value_key    => $row[$value_key],
                    $this->category_key => $row[$category_key]
                ];
            }

            $chart_data[] = [
                $this->category_key => $k,
                $this->series_key => $chart_series
            ];
        }

        $this->data = [];
        return $chart_data;
    }
}
