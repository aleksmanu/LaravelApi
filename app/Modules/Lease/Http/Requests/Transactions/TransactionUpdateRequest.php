<?php

namespace App\Modules\Lease\Http\Requests\Transactions;

use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\PaidStatus;
use App\Modules\Lease\Models\TransactionType;
use Illuminate\Foundation\Http\FormRequest;

class TransactionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'lease_charge_type_id' => 'sometimes|nullable|integer|exists:' . TransactionType::getTableName() . ',id',
            'paid_status_id' => 'sometimes|integer|exists:' . PaidStatus::getTableName() . ',id',
            'invoice_number' => 'sometimes|nullable|string|max:255',
            'amount' => 'sometimes|nullable|numeric',
            'vat' => 'sometimes|nullable|numeric',
            'gross' => 'sometimes|nullable|numeric',
            'gross_received' => 'sometimes|nullable|numeric',
            'due_at' => 'sometimes|nullable|date',
            'paid_at' => 'sometimes|nullable|date',
            'period_from' => 'sometimes|nullable|date',
            'period_to' => 'sometimes|nullable|date',
            'yardi_transaction_ref' => 'sometimes|integer',
            'lease_id' => 'sometimes|integer|exists:' . Lease::getTableName().',id',
        ];
    }
}
