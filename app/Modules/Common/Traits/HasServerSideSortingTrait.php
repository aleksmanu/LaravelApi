<?php
namespace App\Modules\Common\Traits;

trait HasServerSideSortingTrait
{
    /**
     * Transforms nested dot notation into DB tableName.colName format
     * ( a.b.c.d -> str_plural(c) . '.' . d )
     * @param $column
     */
    private function johnifySortColumn(&$column)
    {
        // If it contains dot notation
        if (strpos($column, '.') !== false) {
            $words = explode('.', $column);
            $num_words = count($words);
            $column = str_plural($words[$num_words-2]) . '.' . $words[$num_words-1];
        }
    }
}
