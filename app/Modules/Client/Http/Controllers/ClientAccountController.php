<?php
namespace App\Modules\Client\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Models\AccountType;
use App\Modules\Account\Repositories\AccountRepository;
use App\Modules\Client\Http\Requests\ClientAccounts\ClientAccountDatatableRequest;
use App\Modules\Client\Http\Requests\ClientAccounts\ClientAccountIndexRequest;
use App\Modules\Client\Http\Requests\ClientAccounts\ClientAccountStoreRequest;
use App\Modules\Client\Http\Requests\ClientAccounts\ClientAccountUpdateRequest;
use App\Modules\Client\Repositories\ClientAccountRepository;
use App\Modules\Client\Repositories\Interfaces\IClientAccountRepository;
use App\Modules\Common\Http\Requests\Notes\NoteStoreRequest;
use App\Modules\Common\Models\Address;
use App\Modules\Common\Services\NoteService;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Http\Requests\GetEditAuditTrailRequest;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Services\EditBatchService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Attachments\Traits\ControllerHasAttachments;
use App\Modules\Common\Repositories\AddressRepository;

class ClientAccountController extends Controller
{
    use ControllerHasAttachments;

    /**
     * @var AccountRepository
     */
    protected $account_repository;

    /**
     * @var ClientAccountRepository
     */
    protected $client_account_repository;

    /**
     * @var AddressRepository
     */
    protected $address_repository;

    /**
     * @var EditBatchService
     */
    protected $edit_batch_service;

    /**
     * @var NoteService
     */
    protected $note_service;

    /**
     * @var attachable_repository
     */
    protected $attachable_repository;

    /**
     * ClientAccountController constructor.
     * @param AccountRepository $accountRepository
     * @param ClientAccountRepository $clientAccountRepository
     * @param AddressRepository $addressRepository
     * @param EditBatchService $edit_batch_service
     * @param NoteService $note_service
     */
    public function __construct(
        AccountRepository $accountRepository,
        ClientAccountRepository $clientAccountRepository,
        AddressRepository $addressRepository,
        EditBatchService $edit_batch_service,
        NoteService $note_service
    ) {
        $this->client_account_repository = $clientAccountRepository;
        $this->account_repository        = $accountRepository;
        $this->address_repository        = $addressRepository;
        $this->edit_batch_service        = $edit_batch_service;
        $this->note_service              = $note_service;
        $this->attachable_repository     = $clientAccountRepository;
    }

    /**
     * @param ClientAccountIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(ClientAccountIndexRequest $request)
    {
        return response($this->client_account_repository->getClientAccounts(
            $request->property_manager_id,
            $request->client_account_status_id,
            $request->organisation_type_id
        ));
    }

    /**
     * @param ClientAccountDatatableRequest $request
     * @return \Illuminate\Http\Response
     */
    public function dataTable(ClientAccountDatatableRequest $request)
    {
        return response($this->client_account_repository->getClientAccountDataTable(
            $request->sort_column ?? config('misc.dataTable.defaultSortColumn'),
            $request->sort_order ?? config('misc.dataTable.defaultSortOrder'),
            intval($request->offset),
            $request->limit ?? config('misc.dataTable.defaultPerPage'),
            $request->property_manager_id,
            $request->client_account_status_id,
            $request->organisation_type_id,
            $request->client_account_name_partial
        ));
    }

    /**
     * @param ClientAccountStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function store(ClientAccountStoreRequest $request)
    {
        $validated_data = $request->validated();

        $response = \DB::transaction(function () use ($validated_data) {
            $account = $this->account_repository->storeAccount([
                'account_type_id' => EloquentHelper::getRecordIdBySlug(AccountType::class, AccountType::CLIENT),
                'name'            => $validated_data['name']
            ]);
            $validated_data['account_id'] = $account->id;

            if (!$this->client_account_repository->getImport()) {
                $newAddress = $this->address_repository->storeAddress(Address::toFillableArray($validated_data));
                $validated_data['address_id'] = $newAddress->id;
            }

            $client_account = $this->client_account_repository->storeClientAccount($validated_data);
            return $client_account;
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
        return response($this->client_account_repository->getClientAccount($id));
    }

    public function find(Request $request)
    {
        return response(
            $this->client_account_repository->findClientAccounts($request->input('ids'))
        );
    }

    /**
     * @param ClientAccountUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function update(ClientAccountUpdateRequest $request, $id)
    {

        $client_account = \DB::transaction(function () use ($id, $request) {

            $validated_data = $request->validated();
            $client_account = $this->client_account_repository->getClientAccount($id);

            if (!Auth::user()->canByPassEdit()) {
                $this->edit_batch_service->makeBatch($validated_data, EditBatchType::EDIT, $client_account);

                $client_account = $this->lockClientAccount($id);

                $address = $this->address_repository->getAddress($client_account->address_id);

                if (!$address->reviewStatus->isInReview()) {
                    $this->edit_batch_service->makeBatch(
                        Address::toFillableArray($validated_data),
                        EditBatchType::EDIT,
                        $client_account->address
                    );

                    $this->lockAddress($client_account->address_id);
                }
            } else {
                $this->address_repository->updateAddress(
                    $client_account->address_id,
                    Address::toFillableArray($validated_data)
                );

                $this->account_repository->updateAccount(
                    $client_account->account_id,
                    ['name' => $validated_data['name']]
                );

                $client_account = $this->client_account_repository->updateClientAccount(
                    $client_account->id,
                    $validated_data
                );
            }
            return $client_account;
        });

        return response($client_account);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        return response($this->client_account_repository->deleteClientAccount($id));
    }

    /**
     * @param NoteStoreRequest $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeNote(NoteStoreRequest $request, $id)
    {

        return $this->note_service->make([
            'user_id'     => Auth::user()->id,
            'entity_id'   => $id,
            'entity_type' => $this->client_account_repository->getModelClass(),
            'note'        => $request->note
        ]);
    }

    /**
     * @param  $request
     * @return bool
     */
    public function getEditAuditTrail(GetEditAuditTrailRequest $request, $id)
    {

        $week_ending_date = Carbon::parse($request->week_ending_date);
        $entity           = $this->client_account_repository->getClientAccount($id);

        return $this->edit_batch_service->getEntityAuditTrail($entity, $week_ending_date);
    }

    /**
     * @return mixed
     */
    public function getEditable()
    {

        $model_object = $this->client_account_repository->getModelObject();
        $addr_object  = $this->address_repository->getModelObject();

        return array_merge($model_object->getEditable(), $addr_object->toEditableFieldArray());
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function flag($id)
    {

        $client_account = \DB::transaction(function () use ($id) {
            $client_account = $this->client_account_repository->getClientAccount($id);
            $result = $this->edit_batch_service->makeBatch(
                $client_account->toArray(),
                EditBatchType::FLAG,
                $client_account
            );

            if ($result) {
                $client_account = $this->lockClientAccount($id);
            }

            if (!$client_account->address->reviewStatus->isInReview()) {
                $result = $this->edit_batch_service->makeBatch(
                    $client_account->address->toArray(),
                    EditBatchType::FLAG,
                    $client_account->address
                );

                if ($result) {
                    $this->lockAddress($client_account->address_id);
                }
            }
            return $client_account;
        });

        return $client_account;
    }

    /**
     * @param $id
     * @return \App\Modules\Client\Models\ClientAccount
     */
    private function lockClientAccount($id)
    {

        $locked_at         = Carbon::now();
        $locked_by_user_id = Auth::user()->id;
        $review_status_id  = EloquentHelper::getRecordIdBySlug(ReviewStatus::class, ReviewStatus::IN_REVIEW);

        return $this->client_account_repository->updateClientAccount($id, [
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
