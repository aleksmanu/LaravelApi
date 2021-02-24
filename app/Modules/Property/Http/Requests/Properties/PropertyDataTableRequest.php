<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/20/18
 * Time: 10:30 AM
 */

namespace App\Modules\Property\Http\Requests\Properties;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\PropertyTenure;
use Illuminate\Foundation\Http\FormRequest;

class PropertyDataTableRequest extends FormRequest
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
            'portfolio_id'        => 'sometimes|nullable|integer|exists:' . Portfolio::getTableName() . ',id',
            'property_manager_id' => 'sometimes|nullable|integer|exists:' . PropertyManager::getTableName() . ',id',
            'property_tenure_id'  => 'sometimes|nullable|integer|exists:' . PropertyTenure::getTableName() . ',id',
            'client_account_id'   => 'sometimes|nullable|integer|exists:' . ClientAccount::getTableName() . ',id',
            'offset'              => 'sometimes|nullable|integer',
            'limit'               => 'sometimes|nullable|integer',
            'sort_column'         => 'sometimes|nullable|string',
            'sort_order'          => 'sometimes|nullable|string',
            'property_name_partial' => 'sometimes|string|max:255',
        ];
    }
}
