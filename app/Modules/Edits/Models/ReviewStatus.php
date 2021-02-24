<?php

namespace App\Modules\Edits\Models;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\Model;

class ReviewStatus extends Model
{
    const NEVER_REVIEWED = 'never_reviewed';
    const IN_REVIEW      = 'in_review';
    const REVIEWED       = 'reviewed';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientAccounts()
    {
        return $this->hasMany(ClientAccount::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function isInReview()
    {
        return $this->slug === self::IN_REVIEW;
    }
}
