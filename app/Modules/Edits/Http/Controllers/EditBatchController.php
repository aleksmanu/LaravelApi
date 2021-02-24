<?php

namespace App\Modules\Edits\Http\Controllers;

use App\Modules\Edits\Http\Requests\EditBatchDatatableRequest;
use App\Modules\Edits\Services\EditBatchService;

class EditBatchController
{

    /**
     * @var EditBatchService
     */
    protected $service;

    /**
     * EditBatchController constructor.
     * @param EditBatchService $service
     */
    public function __construct(EditBatchService $service)
    {
        $this->service = $service;
    }

    /**
     * @param EditBatchDatatableRequest $request
     * @return mixed
     */
    public function datatable(EditBatchDatatableRequest $request)
    {
        return response($this->service->datatable(
            $request->edit_batch_type_id,
            $request->offset,
            $request->limit,
            $request->sort_col,
            $request->sort_dir
        ));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function submit($id)
    {
        return $this->service->submit($id);
    }

    /**
     * @param $edit_batch_id
     * @return mixed
     */
    public function getEdits($edit_batch_id)
    {
        return $this->service->getEdits($edit_batch_id);
    }
}
