<?php

namespace App\Modules\Property\Http\Controllers;

use App\Modules\Common\Models\Address;
use App\Modules\Common\Repositories\AddressRepository;
use App\Modules\Common\Services\NoteService;
use App\Modules\Common\Traits\HasNotesController;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Core\Library\MapHelper;
use App\Modules\Edits\Http\Requests\GetEditAuditTrailRequest;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Services\EditBatchService;
use App\Modules\Property\Http\Requests\Properties\PropertyDataTableRequest;
use App\Modules\Property\Http\Requests\Properties\PropertyStoreRequest;
use App\Modules\Property\Http\Requests\Properties\PropertyUpdateRequest;
use App\Modules\Property\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App;
use App\Modules\Attachments\Traits\ControllerHasAttachments;
use App\Modules\Property\Repositories\PropertyRepository;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    use ControllerHasAttachments, HasNotesController;
    /**
     * @var PropertyRepository
     */
    protected $property_repository;
    protected $address_repository;

    /**
     * @var EditBatchService
     */
    protected $edit_service;

    /**
     * @var NoteService
     */
    protected $note_service;

    /**
     * @var attachable_repository
     */
    protected $attachable_repository;

    protected $model;


    /**
     * PropertyController constructor.
     * @param PropertyRepository $propertyRepository
     * @param AddressRepository $addressRepository
     * @param EditBatchService $edit_service
     * @param NoteService $note_service
     * @param Property $model
     */
    public function __construct(
        PropertyRepository $propertyRepository,
        AddressRepository $addressRepository,
        EditBatchService $edit_service,
        NoteService $note_service,
        Property $model
    ) {
        $this->property_repository = $propertyRepository;
        $this->address_repository = $addressRepository;
        $this->edit_service = $edit_service;
        $this->note_service = $note_service;
        $this->attachable_repository = $propertyRepository;
        $this->model = $model;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * int $property_status_id, int $property_use_id, int $property_tenure_id,
     * int $property_category_id, int $location_type_id
     */
    public function index(Request $request)
    {
        return response($this->property_repository->getProperties(
            intval($request->client_account_id),
            intval($request->portfolio_id),
            intval($request->property_manager_id),
            intval($request->property_status_id),
            intval($request->property_use_id),
            intval($request->property_tenure_id),
            intval($request->property_category_id),
            intval($request->location_type_id),
            (bool) $request->include_units
        ));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        return response(
            $this->property_repository->findProperties($request->input('ids'))
        );
    }

    /**
     * @param PropertyDataTableRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function dataTable(PropertyDataTableRequest $request)
    {
        return response($this->property_repository->getPropertyDataTable(
            $request->sort_column ?? config('misc.dataTable.defaultSortColumn'),
            $request->sort_order ?? config('misc.dataTable.defaultSortOrder'),
            intval($request->offset),
            $request->limit ?? config('misc.dataTable.defaultPerPage'),
            intval($request->client_account_id),
            intval($request->portfolio_id),
            intval($request->property_manager_id),
            intval($request->property_status_id),
            intval($request->property_use_id),
            intval($request->property_tenure_id),
            intval($request->property_category_id),
            intval($request->location_type_id),
            $request->property_name_partial
        ));
    }

    /**
     * @param PropertyStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function store(PropertyStoreRequest $request)
    {
        $validated_data = $request->validated();

        $response = \DB::transaction(function () use ($validated_data) {
            $newAddress = $this->address_repository->storeAddress(Address::toFillableArray($validated_data));

            $validated_data['address_id'] = $newAddress->id;

            $property = $this->property_repository->storeProperty($validated_data);
            return $property;
        });

        return response($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->property_repository->getProperty($id));
    }

    /**
     * @param PropertyUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function update(PropertyUpdateRequest $request, int $id)
    {
        $property = \DB::transaction(function () use ($request, $id) {

            $validated_data = $request->validated();

            $property = $this->property_repository->getProperty($id);

            if (!Auth::user()->canByPassEdit()) {
                $result = $this->edit_service->makeBatch($validated_data, EditBatchType::EDIT, $property);

                if ($result) {
                    $this->lockProperty($id);
                }

                $address = $this->address_repository->getAddress($property->address_id);

                if (!$address->reviewStatus->isInReview()) {
                    $result = $this->edit_service->makeBatch(
                        Address::toFillableArray($validated_data),
                        EditBatchType::EDIT,
                        $property->address
                    );

                    if ($result) {
                        $this->lockAddress($property->address_id);
                    }
                }
            } else {
                $this->address_repository->updateAddress(
                    $property['address_id'],
                    Address::toFillableArray($validated_data)
                );
                $property = $this->property_repository->updateProperty($property->id, $validated_data);
            }
            return $property;
        });

        return response($property);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->property_repository->deleteProperty($id));
    }

    //    /**
    //     * @param NoteStoreRequest $request
    //     * @param $id
    //     * @return mixed
    //     * @throws \Illuminate\Validation\ValidationException
    //     */
    //    public function storeNote(NoteStoreRequest $request, $id)
    //    {
    //        return $this->note_service->make([
    //            'user_id'     => Auth::user()->id,
    //            'entity_id'   => $id,
    //            'entity_type' => $this->property_repository->getModelClass(),
    //            'note'        => $request->note
    //        ]);
    //    }

    /**
     * @param  $request
     * @return bool
     */
    public function getEditAuditTrail(GetEditAuditTrailRequest $request, $id)
    {
        $week_ending_date = Carbon::parse($request->week_ending_date);

        $entity = $this->property_repository->getProperty($id);

        return $this->edit_service->getEntityAuditTrail($entity, $week_ending_date);
    }

    /**
     * @return mixed
     */
    public function getEditable()
    {
        $model_object = $this->property_repository->getModelObject();
        $addr_object = $this->address_repository->getModelObject();

        return array_merge($model_object->getEditable(), $addr_object->toEditableFieldArray());
    }

    /**
     * @param $id
     * @return Property
     * @throws \Exception
     * @throws \Throwable
     */
    public function flag($id)
    {
        $property = \DB::transaction(function () use ($id) {

            $property = $this->property_repository->getProperty($id);
            $this->edit_service->makeBatch($property->toArray(), EditBatchType::FLAG, $property);

            $property = $this->lockProperty($id);

            if (!$property->address->reviewStatus->isInReview()) {
                $this->edit_service->makeBatch($property->address->toArray(), EditBatchType::FLAG, $property->address);
                $this->lockAddress($property->address_id);
            }
            return $property;
        });
        return $property;
    }

    public function resolvePostcode(Request $request)
    {
        $data = [];

        if ($request->has('postcode')) {
            $gps_data = MapHelper::geocodePostcode($request->get('postcode'));

            $data['lat']  = $gps_data['lat'];
            $data['long'] = $gps_data['long'];
        }

        return response($data);
    }

    /**
     * @param $id
     * @return Property
     */
    private function lockProperty($id)
    {
        $locked_at         = Carbon::now();
        $locked_by_user_id = Auth::user()->id;
        $review_status_id  = EloquentHelper::getRecordIdBySlug(ReviewStatus::class, ReviewStatus::IN_REVIEW);

        return $this->property_repository->updateProperty($id, [
            'locked_at'         => $locked_at,
            'locked_by_user_id' => $locked_by_user_id,
            'review_status_id'  => $review_status_id
        ]);
    }

    /**
     * @param $id
     * @return Address
     */
    private function lockAddress($id)
    {
        $locked_at         = Carbon::now();
        $locked_by_user_id = Auth::user()->id;
        $review_status_id  = EloquentHelper::getRecordIdBySlug(ReviewStatus::class, ReviewStatus::IN_REVIEW);

        return $this->address_repository->updateAddress($id, [
            'locked_at'         => $locked_at,
            'locked_by_user_id' => $locked_by_user_id,
            'review_status_id'  => $review_status_id
        ]);
    }
}
