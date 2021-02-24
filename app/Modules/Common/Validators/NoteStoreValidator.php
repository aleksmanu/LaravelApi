<?php

namespace App\Modules\Common\Validators;

use App\Modules\Core\Classes\Validator;

class NoteStoreValidator extends Validator
{

    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'user_id'     => 'required|integer|exists:users,id',
            'entity_id'   => 'required|integer',
            'entity_type' => 'required|string',
            'note'        => 'required|string',
            'divider' => 'sometimes|string|nullable'
        ];
    }
}
