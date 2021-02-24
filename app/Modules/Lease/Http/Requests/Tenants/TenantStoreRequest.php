<?php

namespace App\Modules\Lease\Http\Requests\Tenants;

use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Lease\Models\TenantStatus;
use Illuminate\Foundation\Http\FormRequest;

class TenantStoreRequest extends FormRequest
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
            'tenant_status_id'     => 'required|integer|exists:' . TenantStatus::getTableName() . ',id',
            'lease_id'             => 'required|integer|exists:' . Lease::getTableName() . ',id',
            'name'                 => 'required|string|max:255|unique:' . Tenant::getTableName(),
            'yardi_tenant_ref'     => 'required|string|max:16',
            'yardi_tenant_alt_ref' => 'required|string|max:16',
        ];
    }
}
