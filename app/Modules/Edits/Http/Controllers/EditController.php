<?php

namespace App\Modules\Edits\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Edits\Http\Requests\EditRequests\UpdateEditsRequest;
use App\Modules\Edits\Services\EditService;

class EditController extends Controller
{

    /**
     * @var EditService
     */
    private $service;

    /**
     * EditController constructor.
     * @param EditService $service
     */
    public function __construct(EditService $service)
    {
        $this->service = $service;
    }

    /**
     * @param UpdateEditsRequest $request
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function updateEdits(UpdateEditsRequest $request)
    {
        return $this->service->updateEdits($request->edits, $request->approved, $request->note);
    }
}
