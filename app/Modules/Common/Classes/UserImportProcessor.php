<?php


namespace App\Modules\Common\Classes;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Files\ExcelFile;

class UserImportProcessor extends ExcelFile
{
    public function getFile()
    {
        ;
        return Input::file('file');
    }

    public function getFilters()
    {
        return [
            //'chunk'
        ];
    }
}
