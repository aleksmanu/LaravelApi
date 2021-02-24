<?php
namespace App\Modules\Lease\Http\Controllers;

use App\Modules\Lease\Http\Requests\ReviewTypes\ReviewTypeStoreRequest;
use App\Modules\Lease\Http\Requests\ReviewTypes\ReviewTypeUpdateRequest;
use App\Modules\Lease\Repositories\ReviewTypeRepository;

use App\Http\Controllers\Controller;

class ReviewTypeController extends Controller
{
    protected $review_type_repository;

    public function __construct(ReviewTypeRepository $reviewTypeRepository)
    {
        $this->review_type_repository = $reviewTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->review_type_repository->getReviewTypes());
    }

    /**
     * @param ReviewTypeStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(ReviewTypeStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->review_type_repository->storeReviewType($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->review_type_repository->getReviewType($id));
    }

    /**
     * @param ReviewTypeUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(ReviewTypeUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();
        return response($this->review_type_repository->updateReviewType($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->review_type_repository->deleteReviewType($id));
    }
}
