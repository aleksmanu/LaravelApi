<?php
namespace App\Modules\Edits\Repositories;

use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\Edit;
use App\Modules\Edits\Models\EditStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EditRepository
{
    /**
     * @var Edit
     */
    private $model;

    /**
     * EditRepository constructor.
     * @param Edit $model
     */
    public function __construct(Edit $model)
    {
        $this->model = $model;
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
     * @return mixed
     */
    public function insert(array $data)
    {
        return $this->model->insert($data);
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
     * @param $id
     * @return bool
     */
    public function removeNotes($id):bool
    {

        $edit = $this->model->findOrFail($id);
        return $edit->notes()->delete();
    }

    /**
     * @param array $data
     * @param array $ids
     * @return bool
     */
    public function updateMultiple(array $data, array $ids): bool
    {
        return $this->model->whereIn('edits.id', $ids)->update($data);
    }

    /**
     * @param $slug
     * @return int
     */
    public function getEditStatusIdFromSlug($slug): int
    {
        return EloquentHelper::getRecordIdBySlug(EditStatus::class, $slug);
    }

    /**
     * @param Carbon $start_date
     * @param Carbon $end_date
     * @return Collection
     */
    public function getApprovedEditsBetweenDatesByStatus(Carbon $start_date, Carbon $end_date): Collection
    {

        $select = 'COUNT(edits.id) as value,
                   edit_statuses.name as name,
                   CAST(edit_batches.reviewed_at as DATE) as date';

        return $this->model->selectRaw($select)
            ->join('edit_batches', 'edit_batches.id', '=', 'edits.edit_batch_id')
            ->join('edit_statuses', 'edit_statuses.id', '=', 'edits.edit_status_id')
            ->whereNotNull('edit_batches.reviewed_at')
            ->where('edit_batches.reviewed_at', '>=', $start_date)
            ->where('edit_batches.reviewed_at', '<=', $end_date)
            ->where(
                'edits.edit_status_id',
                '!=',
                EloquentHelper::getRecordIdBySlug(
                    EditStatus::class,
                    EditStatus::PENDING
                )
            )->orderBy('date', 'asc')
            ->groupBy(['date', 'edit_statuses.id'])
            ->get();
    }

    /**
     * @return Collection
     */
    public function getEditsApprovalSplit(): Collection
    {

        $select = 'COUNT(edits.id) as value,
                   edit_statuses.name as name';

        return $this->model->selectRaw($select)
            ->join('edit_batches', 'edit_batches.id', '=', 'edits.edit_batch_id')
            ->join('edit_statuses', 'edit_statuses.id', '=', 'edits.edit_status_id')
            ->whereNotNull('edit_batches.reviewed_at')
            ->where(
                'edits.edit_status_id',
                '!=',
                EloquentHelper::getRecordIdBySlug(
                    EditStatus::class,
                    EditStatus::PENDING
                )
            )->groupBy('edit_statuses.id')
            ->get();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getReviewedEditsTotal(): array
    {
        $completed = $this->model
            ->join('edit_batches', 'edit_batches.id', '=', 'edits.edit_batch_id')
            ->whereNotNull('reviewed_at')
            ->count();
        
        $total = $this->model->count();

        return [
            'reviewed' => $completed,
            'total'    => $total
        ];
    }
}
