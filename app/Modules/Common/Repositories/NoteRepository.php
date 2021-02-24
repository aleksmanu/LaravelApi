<?php
namespace App\Modules\Common\Repositories;

use App\Modules\Common\Models\Note;

class NoteRepository
{
    /**
     * @var Note
     */
    private $model;

    /**
     * NoteRepository constructor.
     * @param Note $model
     */
    public function __construct(Note $model)
    {
        $this->model = $model;
    }

    /**
     * @param $entity_id
     * @param $entity_type
     * @return mixed
     */
    public function findAll($entity_id, $entity_type)
    {
        return $this->model->where('entity_id', $entity_id)
            ->where('entity_type', $entity_type)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
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
    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
