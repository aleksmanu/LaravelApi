<?php
namespace App\Modules\Common\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Classes\AddressDataHelper;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Traits\Editable;
use App\Modules\Property\Models\Property;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends BaseModel
{

    use SoftDeletes, Editable;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'addresses';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'county_id',
        'country_id',
        'review_status_id',
        'locked_by_user_id',
        'unit',
        'building',
        'number',
        'street',
        'estate',
        'suburb',
        'town',
        'postcode',
        'latitude',
        'longitude',
        'locked_at'
    ];

    /**
     * @var array
     */
    protected $editable = [
        'county_id',
        'country_id',
        'unit',
        'building',
        'number',
        'street',
        'estate',
        'suburb',
        'town',
        'postcode',
        'latitude',
        'longitude'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to Date objects
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'locked_at'
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'formatted', 'lineFormatted'
    ];

    protected $with = [
        'county',
        'country',
        'reviewStatus',
        'lockedByUser'
    ];

    /**
     * These keys will be ignored from the 'update' method of the model lifecycle
     * @var array
     */
    protected $triggersCorrdinateUpdates = [
        'postcode'
    ];

    protected $ignoredFromUpdatedLifeCycle = [
        'latitude', 'longitude'
    ];

    /**
     * @return string
     */
    public function getFormattedAttribute(): string
    {

        $address = '';

        $this->formattedAttributeLogicExtraction($address, $this->unit, ' ');
        $this->formattedAttributeLogicExtraction($address, $this->building, ', ');
        $this->formattedAttributeLogicExtraction($address, $this->number, ' ');
        $this->formattedAttributeLogicExtraction($address, $this->street, ' ');
        $this->formattedAttributeLogicExtraction($address, $this->suburb, ', ');
        $this->formattedAttributeLogicExtraction($address, $this->town, ', ');

        if ($this->county) {
            $this->formattedAttributeLogicExtraction($address, $this->county->name, ', ');
        }

        if ($this->country) {
            $this->formattedAttributeLogicExtraction($address, $this->country->name, ', ');
        }

        $this->formattedAttributeLogicExtraction($address, $this->postcode, ', ');
        return $address;
    }

    public function getLineFormattedAttribute(): string
    {
        $address = '';

        if ($this->building) {
            $this->formattedAttributeLogicExtraction($address, $this->unit, ' ');
            $this->formattedAttributeLogicExtraction($address, $this->building, "\r\n");
        }
        $this->formattedAttributeLogicExtraction($address, $this->number, ' ');
        $this->formattedAttributeLogicExtraction($address, $this->street, ' ');
        $this->formattedAttributeLogicExtraction($address, $this->suburb, "\r\n");
        $this->formattedAttributeLogicExtraction($address, $this->town, "\r\n");

        if ($this->county) {
            $this->formattedAttributeLogicExtraction($address, $this->county->name, "\r\n");
        }

        if ($this->country) {
            $this->formattedAttributeLogicExtraction($address, $this->country->name, "\r\n");
        }

        $this->formattedAttributeLogicExtraction($address, $this->postcode, "\r\n");
        return $address;
    }

    /**
     * Model lifecycle hooks
     */
    protected static function boot()
    {
        parent::boot();
        static::updated(function ($address) {
            $dirtyCount = count(array_keys($address->getDirty()));
            $reducedDirtyCount = count(
                array_diff(array_keys($address->getDirty()), $address->triggersCorrdinateUpdates)
            );
            $ignoredDirtyCount = count(
                array_diff(array_keys($address->getDirty()), $address->ignoredFromUpdatedLifeCycle)
            );
            if ($reducedDirtyCount !== $dirtyCount && !$ignoredDirtyCount) {
                $coords = AddressDataHelper::getGpsData($address->postcode);

                $address['latitude'] = $coords['latitude'];
                $address['longitude'] = $coords['longitude'];
                $address->save();
            }
        });
    }

    /**
     * @param string &$address_string
     * @param string $address_attribute
     * @param string $separator
     */
    private function formattedAttributeLogicExtraction(
        string &$address_string,
        $address_attribute,
        string $separator
    ) {
        if ($address_attribute) {
            if ($address_string == '') {
                $address_string .= $address_attribute;
            } else {
                $address_string .= $separator . $address_attribute;
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function county()
    {
        return $this->belongsTo(County::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientAccounts()
    {
        return $this->hasMany(ClientAccount::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by_user_id');
    }

    public function toAddrArray()
    {
        return [
            'addr_unit'     => $this->unit,
            'addr_number'   => $this->number,
            'addr_building' => $this->building,
            'addr_street'   => $this->street,
            'addr_estate'   => $this->estate,
            'addr_suburb'   => $this->suburb,
            'addr_town'     => $this->town,
            'addr_postcode' => $this->postcode,

            'county_id'  => $this->county ? $this->county->id : null,
            'country_id' => $this->country ? $this->country->id : null
        ];
    }

    /**
     * @param array $addr_array
     * @return array
     */
    public static function toFillableArray(array $addr_array)
    {
        $arr = [
            'unit'       => $addr_array['addr_unit'],
            'number'     => $addr_array['addr_number'],
            'building'   => $addr_array['addr_building'],
            'street'     => $addr_array['addr_street'],
            'estate'     => $addr_array['addr_estate'],
            'suburb'     => $addr_array['addr_suburb'],
            'town'       => $addr_array['addr_town'],
            'postcode'   => $addr_array['addr_postcode'],
            'county_id'  => $addr_array['county_id'] ?? null,
            'country_id' => $addr_array['country_id'] ?? null
        ];

        if (array_key_exists('addr_lat', $addr_array) &&
            array_key_exists('addr_long', $addr_array) &&
            $addr_array['addr_lat'] &&
            $addr_array['addr_long']
        ) {
            $arr['latitude'] = $addr_array['addr_lat'];
            $arr['longitude'] = $addr_array['addr_long'];
        }


        return $arr;
    }

    /**
     * TEMP
     * @return array
     */
    public function toEditableFieldArray()
    {
        return [
            'county_id',
            'country_id',
            'addr_unit',
            'addr_building',
            'addr_number',
            'addr_street',
            'addr_estate',
            'addr_suburb',
            'addr_town',
            'addr_postcode',
        ];
    }
}
