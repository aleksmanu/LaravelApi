<?php

namespace App\Modules\Core\Library\ChartLibrary;

use Carbon\Carbon;

abstract class ChartParser
{

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $category_key = 'name';

    /**
     * @var string
     */
    protected $value_key = 'value';

    /**
     * @var string
     */
    protected $series_key = 'series';

    /**
     * @param Carbon $start_date
     * @param Carbon $end_date
     * @param string $date_key
     * @param int $default_value
     * @return $this
     */
    public function setDatesBetweenSeries(Carbon $start_date, Carbon $end_date, string $date_key, $default_value = 0)
    {

        while ($start_date <= $end_date) {
            foreach ($this->data as $k => &$series) {
                $found = array_search($start_date->toDateString(), array_column($series, $date_key));
                if ($found === false) {
                    $series[] = [$this->value_key => $default_value, $date_key => $start_date->toDateString()];
                }
            }

            $start_date->addDay();
        }
        return $this;
    }

    /**
     * @param null $key
     * @return $this
     */
    public function sortSeriesByKey($key = null)
    {

        $key = $key ?: $this->value_key;

        foreach ($this->data as $k => &$series) {
            usort($series, function ($a, $b) use ($key) {
                return $a[$key] <=> $b[$key];
            });
        }
        return $this;
    }

    /**
     * Load data for the parser
     * @param array $data
     * @return mixed
     */
    public function load(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param string $category_key
     * @param string $value_key
     * @return array
     */
    abstract public function get($category_key = '', $value_key = ''): array;
}
