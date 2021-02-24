<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/26/18
 * Time: 9:53 AM
 */

namespace App\Modules\Property\Http\Requests\Units;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Property\Models\PropertyManager;
use Illuminate\Foundation\Http\FormRequest;

class UnitDataTableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // More granular rules can go here, return false to halt
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_manager_id' => 'nullable|integer|exists:' . PropertyManager::getTableName() . ',id',
            'client_account_id' => 'nullable|integer|exists:' . ClientAccount::getTableName() . ',id',
            'offset' => 'nullable|integer',
            'limit' => 'nullable|integer',
            'sort_column' => 'nullable|string',
            'sort_order' => 'nullable|string'
        ];
    }
}
