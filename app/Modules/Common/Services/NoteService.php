<?php

namespace App\Modules\Common\Services;

use App\Modules\Common\Repositories\NoteRepository;
use App\Modules\Common\Validators\NoteStoreValidator;

class NoteService
{

    /**
     * @var NoteRepository
     */
    protected $repository;

    /**
     * NoteService constructor.
     * @param NoteRepository $repository
     */
    public function __construct(NoteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $note_data
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function make(array $note_data)
    {

        $this->validate($note_data);

        return $this->repository->store($note_data);
    }

    /**
     * @param array $note_data
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function firstOrCreate(array $note_data)
    {

        $this->validate($note_data);

        return $this->repository->create($note_data);
    }

    /**
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validate(array $data)
    {

        $validator = new NoteStoreValidator();
        $validator->validate($data);
    }
}
