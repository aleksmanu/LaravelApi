<?php
namespace App\Modules\Edits\Services;

use App\Modules\Common\Services\NoteService;
use App\Modules\Core\Library\StringHelper;
use App\Modules\Edits\Exceptions\RejectedEditException;
use App\Modules\Edits\Models\Edit;
use App\Modules\Edits\Models\EditStatus;
use App\Modules\Edits\Repositories\EditBatchRepository;
use App\Modules\Edits\Repositories\EditRepository;
use App\Modules\Edits\Repositories\EditStatusRepository;
use Illuminate\Support\Facades\Auth;

class EditService
{

    /**
     * @var EditRepository
     */
    protected $repository;

    /**
     * @var EditBatchRepository
     */
    protected $edit_batch_repository;

    /**
     * @var EditStatusRepository
     */
    protected $edit_status_repository;

    /**
     * @var NoteService
     */
    protected $note_service;

    /**
     * EditService constructor.
     * @param EditRepository $repository
     * @param EditBatchRepository $edit_batch_repository
     * @param EditStatusRepository $edit_status_repository
     * @param NoteService $note_service
     */
    public function __construct(
        EditRepository $repository,
        EditBatchRepository $edit_batch_repository,
        EditStatusRepository $edit_status_repository,
        NoteService $note_service
    ) {
        $this->repository             = $repository;
        $this->edit_batch_repository  = $edit_batch_repository;
        $this->edit_status_repository = $edit_status_repository;
        $this->note_service           = $note_service;
    }

    /******************************** PUBLIC METHODS *************************************/

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
     * @param array $field_data
     * @param $edit_batch_id
     * @param $entity
     * @return mixed
     */
    public function insert(array $field_data, $edit_batch_id, $entity)
    {

        $default_status = $this->repository->getEditStatusIdFromSlug(EditStatus::PENDING);

        $edits_data = [];
        foreach ($field_data as $k => $v) {
            $foreign_entity = $this->checkIsForeign($k) ? $this->getEntityClassFromField($k, $entity) : null;
            $edits_data[]   = [
                'edit_batch_id'  => $edit_batch_id,
                'edit_status_id' => $default_status,
                'field'          => $k,
                'value'          => $v,
                'previous_value' => optional($entity)->$k,
                'foreign_entity' => $foreign_entity
            ];
        }
        return $this->repository->insert($edits_data);
    }

    /**
     * @param array $edits
     * @param bool $approved
     * @param $note
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function updateEdits(array $edits, bool $approved, $note)
    {

        \DB::transaction(function () use ($edits, $approved, $note) {

            $slug = $approved ? EditStatus::APPROVED : EditStatus::REJECTED;

            $this->updateStatuses($edits, $slug);

            if ($note) {
                foreach ($edits as $edit) {
                    $this->createNote($edit, $approved, $note);
                }
            }

            return $edits;
        });

        return $edits;
    }

    /******************************** PROTECTED METHODS ***********************************/

    /******************************** PRIVATE METHODS ************************************/

    /**+
     * @param array $ids
     * @param $slug
     */
    private function updateStatuses(array $ids, $slug)
    {
        if ($ids) {
            $status = $this->edit_status_repository->findBySlug($slug);
            $this->repository->updateMultiple(['edit_status_id' => $status->id], $ids);
        }
    }

    /**
     * @param $str
     * @return bool
     */
    private function checkIsForeign($str)
    {
        $check_str = substr($str, -3);
        return $check_str === '_id';
    }

    /**
     * @param $str
     * @param $entity
     * @return string
     */
    private function getEntityClassFromField($str, $entity)
    {

        $class = StringHelper::snakeCaseToCamelCase(rtrim($str, '_id'));
        return get_class($entity->$class()->getRelated());
    }

    /**
     * @param $id
     * @param bool $approved
     * @param $note
     * @return mixed
     * @throws RejectedEditException
     * @throws \Illuminate\Validation\ValidationException
     */
    private function createNote($id, bool $approved, $note)
    {

        if (!$approved && !$note) {
            throw new RejectedEditException('Rejected edits must have a note');
        }

        $this->repository->removeNotes($id); //Remove any notes that already exists. Only one note per edit allowed
        return $this->note_service->firstOrCreate([
            'user_id'     => Auth::user()->id,
            'entity_type' => Edit::class,
            'entity_id'   => $id,
            'note'        => $note
        ]);
    }
}
