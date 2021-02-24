<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/26/18
 * Time: 10:48 AM
 */

return [
    'dataTable' => [
        'defaultPerPage' => env('MISC_DATATABLE_DEFAULT_PERPAGE', 10),
        'defaultSortColumn' => env('MISC_DATATABLE_DEFAULT_SORTCOLUMN', 'id'),
        'defaultSortOrder' => env('MISC_DATATABLE_DEFAULT_SORTORDER', 'asc')
    ],
    'api' => [
        'maximumResponseSize' => env('MISC_MAX_RESPONSE_SIZE', 9999),
    ],
];