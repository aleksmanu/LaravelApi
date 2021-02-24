<?php

namespace App\Modules\Client\Http\Requests\OrganisationTypes;

use App\Modules\Client\Models\OrganisationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganisationTypeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Bouncer::can('update', OrganisationType::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(OrganisationType::getTableName())->ignore($this->route('organisationType'))
            ],
        ];
    }
}
