<?php
namespace App\Modules\Property\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Property\Http\Requests\StopPostings\StopPostingStoreRequest;
use App\Modules\Property\Http\Requests\StopPostings\StopPostingUpdateRequest;
use App\Modules\Property\Repositories\StopPostingRepository;

class StopPostingController extends Controller
{
    /**
     * @var StopPostingRepository
     */
    protected $stop_posting_repository;

    /**
     * StopPostingController constructor.
     * @param StopPostingRepository $stopPostingRepository
     */
    public function __construct(StopPostingRepository $stopPostingRepository)
    {
        $this->stop_posting_repository = $stopPostingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->stop_posting_repository->getStopPostings());
    }

    /**
     * @param StopPostingStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(StopPostingStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->stop_posting_repository->storeStopPosting($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->stop_posting_repository->getStopPosting($id));
    }

    /**
     * @param StopPostingUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(StopPostingUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        return response($this->stop_posting_repository->updateStopPosting($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->stop_posting_repository->deleteStopPosting($id));
    }
}
