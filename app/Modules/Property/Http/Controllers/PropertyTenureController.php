<?php
namespace App\Modules\Property\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Library\StringHelper;
use App\Modules\Property\Http\Requests\Tenures\PropertyTenureStoreRequest;
use App\Modules\Property\Http\Requests\Tenures\PropertyTenureUpdateRequest;
use App\Modules\Property\Repositories\PropertyTenureRepository;

class PropertyTenureController extends Controller
{
    /**
     * @var PropertyTenureRepository
     */
    protected $property_tenure_repository;

    /**
     * PropertyTenureController constructor.
     * @param PropertyTenureRepository $property_tenure_repository
     */
    public function __construct(PropertyTenureRepository $property_tenure_repository)
    {
        $this->property_tenure_repository = $property_tenure_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->property_tenure_repository->getTenures());
    }

    /**
     * @param PropertyTenureStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(PropertyTenureStoreRequest $request)
    {
        $validated_data = $request->validated();

        $validated_data['slug'] = StringHelper::slugify($validated_data['name']);
        return response($this->property_tenure_repository->storeTenure($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->property_tenure_repository->getTenure($id));
    }

    /**
     * @param PropertyTenureUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(PropertyTenureUpdateRequest $request, $id)
    {
        $validated_data         = $request->validated();
        $validated_data['slug'] = StringHelper::slugify($validated_data['name']);
        return response($this->property_tenure_repository->updateTenure($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->property_tenure_repository->deleteTenure($id));
    }
}
