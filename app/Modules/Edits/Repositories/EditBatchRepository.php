<?php

namespace App\Modules\Edits\Repositories;

use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\EditBatch;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Edits\Models\EditStatus;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Property\Models\PropertyManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EditBatchRepository
{
    /**
     * @var EditBatch
     */
    private $model;

    /**
     * EditBatchRepository constructor.
     * @param EditBatch $model
     */
    public function __construct(EditBatch $model)
    {
        $this->model = $model;
    }

    /**
     * @param $edit_batch_type_id
     * @param $offset
     * @param $limit
     * @param $sort_col
     * @param $sort_dir
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|mixed|static[]
     */
    public function getEditBatchesDatatable($edit_batch_type_id, $offset, $limit, $sort_col, $sort_dir)
    {

        $query = $this->model->newQueryWithoutRelationships()
            ->with(['editBatchType', 'createdByUser', 'reviewedByUser'])
            ->skip($offset)
            ->withCount('edits')
            ->orderBy($sort_col, $sort_dir)
            ->whereNull('edit_batches.reviewed_at')
            ->take($limit);

        $count = $query->count();

        $query->skip($offset)
            ->take($limit);

        if ($edit_batch_type_id) {
            $query->where('edit_batches.edit_batch_type_id', $edit_batch_type_id);
        }

        // $result = $query->get()->each(function (&$batches) {
        //     $batches->append(['client_account']);
        // });

        return collect([
            'rows'      => $query->get(),
            'row_count' => $count
        ]);
    }

    /**
     * @param $id
     * @return EditBatch
     */
    public function findById($id): EditBatch
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        return $this->model->findOrFail($id)->update($data);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function submit(int $id)
    {

        $reviewed_at = Carbon::now();
        $edit_batch  = $this->model->findOrFail($id);

        $edit_batch->update([
            'reviewed_at'         => $reviewed_at,
            'reviewed_by_user_id' => Auth::user()->id,
            'status_changed_at'   => $reviewed_at
        ]);

        $edit_batch->entity->update([
            'locked_at'         => null,
            'locked_by_user_id' => null,
            'review_status_id'  => EloquentHelper::getRecordIdBySlug(ReviewStatus::class, ReviewStatus::REVIEWED)
        ]);
        return $edit_batch;
    }

    /**
     * @param int $edit_batch_id
     * @return bool
     */
    public function checkForPendingEdits(int $edit_batch_id): bool
    {

        $edit_batch_batch = $this->model->findOrFail($edit_batch_id);

        $edit_pending_status = EloquentHelper::getRecordIdBySlug(EditStatus::class, EditStatus::PENDING);
        $edit_count          = $edit_batch_batch->edits()->where('edit_status_id', $edit_pending_status)->count();

        return $edit_count !== 0;
    }

    /**
     * @param $slug
     * @return int
     */
    public function getEditBatchTypeIdFromSlug($slug)
    {
        return EloquentHelper::getRecordIdBySlug(EditBatchType::class, $slug);
    }

    /**
     * @param $edit_batch_id
     * @return Collection
     */
    public function getApprovedEditsByBatch($edit_batch_id): Collection
    {

        $edit_batch = $this->model->findOrFail($edit_batch_id);

        return $edit_batch->edits()
            ->where(
                'edit_status_id',
                EloquentHelper::getRecordIdBySlug(
                    EditStatus::class,
                    EditStatus::APPROVED
                )
            )->get();
    }

    /**
     * @param $edit_batch_id
     * @param array $entity_data
     * @return mixed
     */
    public function updateEntity($edit_batch_id, array $entity_data)
    {
        $edit_batch = $this->model->findOrFail($edit_batch_id);
        return $edit_batch->entity->update($entity_data);
    }

    /**
     * @param $entity
     * @param Carbon $week_ending_date
     * @return Collection
     */
    public function editBatchAuditData($entity, Carbon $week_ending_date): Collection
    {

        $week_starting = $week_ending_date->copy()->startOfWeek();

        $entity_audit_query = $this->getEditBatchAuditQuery(
            $entity->id,
            get_class($entity),
            $week_starting,
            $week_ending_date
        );

        if ($entity->address) {
            $address_audit_query = $this->getEditBatchAuditQuery(
                $entity->address_id,
                get_class($entity->address),
                $week_starting,
                $week_ending_date
            );

            $entity_audit_query = $entity_audit_query->union($address_audit_query);
        }

        $result = $entity_audit_query
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($result as $batch) {
            foreach ($batch->edits as $edit) {
                if ($edit->foreign_entity) {
                    $prevEntityIsPropManager = get_class($edit->previousForeignEntity) === PropertyManager::class;
                    if ($edit->previousForeignEntity && $prevEntityIsPropManager) {
                        $edit->previousForeignEntity->name = $edit->previousForeignEntity->user->first_name . ' '
                            . $edit->previousForeignEntity->user->last_name;
                        $edit->proposedForeignEntity->name = $edit->proposedForeignEntity->user->first_name . ' '
                            . $edit->proposedForeignEntity->user->last_name;
                    } else {
                        $edit->previousForeignEntity;
                        $edit->proposedForeignEntity;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param $entity_id
     * @param $entity_type
     * @param Carbon $week_starting
     * @param Carbon $week_ending
     * @return Builder
     */
    private function getEditBatchAuditQuery(
        $entity_id,
        $entity_type,
        Carbon $week_starting,
        Carbon $week_ending
    ): Builder {

        return $this->model->with([
            'createdByUser',
            'reviewedByUser',
            'edits.editStatus',
            'edits.notes',
            'editBatchType',
            'edits' => function ($query) {
                $query->orderBy('edits.updated_at', 'desc');
            }
        ])->where('entity_id', $entity_id)
            ->where('entity_type', $entity_type)
            ->where('edit_batches.created_at', '>=', $week_starting)
            ->where('edit_batches.created_at', '<=', $week_ending);
    }

    /**
     * @param $edit_batch_id
     * @return mixed
     */
    public function getEdits($edit_batch_id)
    {

        $batch = $this->model->with(['edits.notes.user', 'edits.editStatus'])->findOrFail($edit_batch_id);

        //Get polymorphic relationships, this doesn't work with eager loading
        foreach ($batch->edits as $edit) {
            if ($edit->foreign_entity) {
                $prevEntityIsPropManager = get_class($edit->previousForeignEntity) === PropertyManager::class;
                if ($edit->previousForeignEntity && $prevEntityIsPropManager) {
                    $edit->previousForeignEntity->name = $edit->previousForeignEntity->user->first_name . ' '
                        . $edit->previousForeignEntity->user->last_name;
                    $edit->proposedForeignEntity->name = $edit->proposedForeignEntity->user->first_name . ' '
                        . $edit->proposedForeignEntity->user->last_name;
                } else {
                    $edit->previousForeignEntity;
                    $edit->proposedForeignEntity;
                }
            }
        }
        return $batch;
    }
}
