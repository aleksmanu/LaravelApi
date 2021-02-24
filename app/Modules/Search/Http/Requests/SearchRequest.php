<?php
namespace App\Modules\Search\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $scope
 * @property-read string $search
 */

class SearchRequest extends FormRequest
{
    private $scope;
    private $search;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // More granular rules can go here, return false to halt
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'search' => 'required|string',
            'scope'  => 'required|string',
        ];
    }
}
