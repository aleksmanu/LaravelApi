<?php
namespace App\Modules\Lease\Traits;

use App\Modules\Lease\Models\LeaseBreak;
use Carbon\Carbon;

trait HasLeaseBreaksTrait
{
    public function leaseBreaks()
    {
        return $this->morphMany(LeaseBreak::class, 'entity');
    }

    public function nextLeaseBreak()
    {
        return $this->leaseBreaks()
            ->where('date', '>=', Carbon::now())
            ->orderBy('date', 'asc');
    }

    public function getNextBreakAttribute()
    {
        $breaks = $this->nextLeaseBreak;
        if ($breaks->count()) {
            return $breaks[0];
        }

        return false;
    }

    public function getNextBreakDateAttribute()
    {
        $breaks = $this->nextLeaseBreak;
        if ($breaks->count()) {
            return $breaks[0]->date;
        }

        return false;
    }
}
