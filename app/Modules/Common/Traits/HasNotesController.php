<?php
namespace App\Modules\Common\Traits;

use App\Modules\Common\Http\Requests\Notes\NoteStoreRequest;
use App\Modules\Common\Repositories\NoteRepository;

trait HasNotesController
{
    public function storeNote(int $assetId, NoteStoreRequest $request)
    {
        $valid = $request->validated();

        $newNote = $this->getNoteRepo()->store([
            'user_id'     => auth()->user()->id,
            'entity_id'   => $assetId,
            'entity_type' => get_class($this->model),
            'note'        => $valid['note'],
            'is_internal' => $valid['is_internal'] ?? true,   //default to true to avoid dummies spilling their beans
            'divider'     => $valid['divider'] ?? null
        ]);

        return response($newNote->load('user'));
    }

    public function getNotes(int $assetId)
    {
        return response($this->getNoteRepo()->findAll($assetId, get_class($this->model)));
    }

    private function getNoteRepo()
    {
        return app()->make(NoteRepository::class);
    }
}
