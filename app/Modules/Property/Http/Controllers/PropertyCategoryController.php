<?php
namespace App\Modules\Property\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Property\Http\Requests\PropertyCategories\PropertyCategoryStoreRequest;
use App\Modules\Property\Http\Requests\PropertyCategories\PropertyCategoryUpdateRequest;
use App\Modules\Property\Repositories\PropertyCategoryRepository;

class PropertyCategoryController extends Controller
{
    /**
     * @var PropertyCategoryRepository
     */
    protected $stop_posting_repository;

    /**
     * PropertyCategoryController constructor.
     * @param PropertyCategoryRepository $stopPostingRepository
     */
    public function __construct(PropertyCategoryRepository $stopPostingRepository)
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
        return response($this->stop_posting_repository->getPropertyCategories());
    }

    /**
     * @param PropertyCategoryStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(PropertyCategoryStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->stop_posting_repository->storePropertyCategory($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->stop_posting_repository->getPropertyCategory($id));
    }

    /**
     * @param PropertyCategoryUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(PropertyCategoryUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        return response($this->stop_posting_repository->updatePropertyCategory($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->stop_posting_repository->deletePropertyCategory($id));
    }
}
