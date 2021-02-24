<?php

namespace App\Modules\Acquisition\Models;

use Illuminate\Database\Eloquent\Model;

class StepGroup extends Model
{
    protected $table = 'acquisition_step_groups';

    protected $fillable = [
        "name",
        "order",
    ];
}
