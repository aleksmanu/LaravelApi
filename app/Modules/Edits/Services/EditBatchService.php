<?php
namespace App\Modules\Edits\Services;

use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Repositories\EditBatchRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Purpose: To handle business logic for edit batches
 * Class EditBatchService
 * @package App\Modules\Edits\Services
 */
class EditBatchService
{

    /**
     * @var EditBatchRepository
     */
    protected $repository;

    /**
     * @var EditService
     */
    protected $edit_service;

    /**
     * EditBatchService constructor.
     * @param EditBatchRepository $repository
     * @param EditService $edit_service
     */
    public function __construct(EditBatchRepository $repository, EditService $edit_service)
    {
        $this->repository   = $repository;
        $this->edit_service = $edit_service;
    }

    /******************************** PUBLIC METHODS *************************************/

    /**
     * @param array $entity_data
     * @param $edit_batch_type
     * @param $entity
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function makeBatch(array $entity_data, $edit_batch_type, $entity)
    {

        $edit_batch = \DB::transaction(function () use ($entity_data, $edit_batch_type, $entity) {

            if ($edit_batch_type === EditBatchType::EDIT) {
                $record_data = array_only($entity->toArray(), $entity->getEditable());
                $field_data  = array_diff(array_only($entity_data, $entity->getEditable()), $record_data);
            } else {
                $field_data = array_only($entity_data, $entity->getEditable());
            }

            if (empty($field_data)) {
                return false;
            }
            $edit_batch_data = [
                'edit_batch_type_id' => $this->repository->getEditBatchTypeIdFromSlug($edit_batch_type),
                'created_by_user_id' => Auth::user()->id,
                'entity_type'        => get_class($entity),
                'entity_id'          => optional($entity)->id,
                'status_changed_at'  => Carbon::now(),
                'name'               => $entity->getEditableName()
            ];

            $edit_batch = $this->make($edit_batch_data);

            $this->edit_service->insert($field_data, $edit_batch->id, $entity);
            return $edit_batch;
        });

        return $edit_batch;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function make(array $data)
    {
        return $this->repository->store($data);
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        return $this->repository->update($data, $id);
    }

    /**
     * @param $edit_batch_type_id
     * @param $offset
     * @param $limit
     * @param $sort_col
     * @param $sort_dir
     * @return mixed
     */
    public function datatable($edit_batch_type_id, $offset, $limit, $sort_col, $sort_dir)
    {
        return $this->repository->getEditBatchesDatatable($edit_batch_type_id, $offset, $limit, $sort_col, $sort_dir);
    }

    /**
     * @param $edit_batch_id
     * @return mixed
     */
    public function submit($edit_batch_id)
    {

        $has_pending = $this->repository->checkForPendingEdits($edit_batch_id);


        if ($has_pending) {
            throw new \LogicException("Edit batch has pending edits");
        }

        $date = Carbon::now();

        $this->repository->update([
                                      'reviewed_by_user_id' => Auth::user()->id,
                                      'status_changed_at'   => $date,
                                      'reviewed_at'         => $date
                                  ], $edit_batch_id);

        $this->updateEntity($edit_batch_id);

        $rejected_count = 0;
        $batch = $this->getEdits($edit_batch_id);
        foreach ($batch['edits'] as $edit) {
            $rejected_count += $edit->editStatus->isRejected();
        }

        return [
            'rejected_count' => $rejected_count,
            'edit_batch' => $batch
        ];
    }

    /**
     * @param $entity
     * @param Carbon $week_ending_date
     * @return mixed
     */
    public function getEntityAuditTrail($entity, Carbon $week_ending_date)
    {

        return $this->repository->editBatchAuditData($entity, $week_ending_date);
    }

    /**
     * @param $edit_batch_id
     * @return mixed
     */
    public function getEdits($edit_batch_id)
    {
        return $this->repository->getEdits($edit_batch_id);
    }
    /******************************** PRIVATE METHODS ************************************/


    /**
     * @param $edit_batch_id
     * @return mixed
     */
    private function updateEntity($edit_batch_id)
    {

        $edits = $this->repository->getApprovedEditsByBatch($edit_batch_id);

        $entity_data = [];
        foreach ($edits as $edit) {
            $entity_data[$edit->field] = $edit->value;
        }

        $entity_data['locked_at']         = null;
        $entity_data['locked_by_user_id'] = null;
        $entity_data['review_status_id']  = EloquentHelper::getRecordIdBySlug(
            ReviewStatus::class,
            ReviewStatus::REVIEWED
        );

        return $this->repository->updateEntity($edit_batch_id, $entity_data);
    }
}
