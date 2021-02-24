<?php
namespace App\Modules\Workorder\Http\Requests\Quotes;

use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use App\Modules\Workorder\Models\Supplier;
use App\Modules\Workorder\Models\ExpenditureType;
use App\Modules\Auth\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Property\Models\PropertyManager;

class QuoteDataTableRequest extends FormRequest
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
            'property_id'         => 'sometimes|nullable|integer|exists:' . Property::getTableName() . ',id',
            'unit_id'             => 'sometimes|nullable|integer|exists:' . Unit::getTableName() . ',id',
            'supplier_id'         => 'sometimes|nullable|integer|exists:' . Supplier::getTableName() . ',id',
            'expenditure_type_id' => 'sometimes|nullable|integer|exists:' . ExpenditureType::getTableName() . ',id',
            'rejected_by'         => 'sometimes|nullable|integer|exists:' . User::getTableName() . ',id',
            'client_account_id'   => 'sometimes|nullable|integer|exists:' . ClientAccount::getTableName() . ',id',
            'portfolio_id'        => 'sometimes|nullable|integer|exists:' . Portfolio::getTableName() . ',id',
            'property_manager_id' => 'sometimes|nullable|integer|exists:' . PropertyManager::getTableName() . ',id',
            'type'                => 'sometimes|nullable|string',
        ];
    }
}
