<?php

namespace App\Modules\Core\Interfaces;

interface IYardiImport
{

    /**
     * Used to handle and import records via Excel
     * @param array $data
     * @return mixed
     */
    public function importRecord(array $data);
}
