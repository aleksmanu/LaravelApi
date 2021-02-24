<?php

namespace App\Modules\Client\Http\Requests\Portfolios;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Property\Models\PropertyManager;
use Illuminate\Foundation\Http\FormRequest;

class PortfolioStoreRequest extends FormRequest
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
            'client_account_id'   => 'required|integer|exists:' . ClientAccount::getTableName() . ',id',
            'name'                => 'required|string|max:255',
            'yardi_portfolio_ref' => 'required|string|max:20'
        ];
    }
}
