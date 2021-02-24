<?php

namespace App\Modules\Edits\Traits;

use App\Modules\Common\Models\Address;
use App\Modules\Lease\Models\Lease;
use App\Modules\Property\Models\Unit;

trait Editable
{

    /**
     * @return array
     * @throws \Exception
     */
    public function getEditable(): array
    {

        if (!property_exists(self::class, 'editable')) {
            throw new \Exception(self::class . ' must have an $editable attribute');
        }
        return $this->editable;
    }

    /**
     * @return string
     */
    public function getEditableName(): string
    {
        $class = get_class($this);

        if ($class === Unit::class) {
            return $this->demise;
        } elseif ($class === Lease::class) {
            return 'Lease: ' . $this->yardi_tenant_ref;
        } elseif ($class === Address::class) {
            return 'Address: ' . $this->number . ' ' . $this->street . ' ' . $this->postcode;
        } else {
            return $this->name;
        }
    }
}
