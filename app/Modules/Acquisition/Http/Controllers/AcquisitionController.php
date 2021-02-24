<?php
namespace App\Modules\Acquisition\Http\Controllers;

use App\Modules\Account\Models\Account;
use App\Modules\Acquisition\Http\Requests\AcquisitionCreateRequest;
use App\Modules\Acquisition\Models\Acquisition;

use App\Modules\Acquisition\Repositories\AcquisitionRepository;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Modules\Acquisition\Http\Requests\AcquisitionUpdateRequest;

class AcquisitionController extends Controller
{
    protected $repository;

    public function __construct(AcquisitionRepository $acquisitionRepository)
    {
        $this->repository = $acquisitionRepository;
    }

    public function show($acquisition)
    {
        return response($this->repository->get($acquisition));
    }

    public function update($acquisition, AcquisitionUpdateRequest $request)
    {
        return response($this->repository->update($acquisition, $request->validated()));
    }

    public function create(AcquisitionCreateRequest $request)
    {
        $valid = $request->validated();

        if (Acquisition::where('account_id', $valid['account_id'])->where('name', $valid['name'])->exists()) {
            $account = Account::find($valid['account_id']);
            throw ValidationException::withMessages([
                'Client ID & Region Reference' => [
                    'Region reference "' . $valid['name'] . '" already exists for Account "' . $account->name . '".'
                ]
            ]);
        }

        return response($this->repository->create($valid));
    }

    public function deletePopArea($pop_area)
    {
        $response = $this->repository->deletePopArea($pop_area);

        if (!$response) {
            return response('Unable to delete. POP area still has sites', 422);
        } else {
            return response($response);
        }
    }

    public function index()
    {
        return 'asdf';
    }
}
