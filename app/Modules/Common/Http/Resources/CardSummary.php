<?php
namespace App\Modules\Common\Http\Resources;

use Illuminate\Database\Eloquent\Model;

class CardSummary extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total', 'reviewed', 'pending', 'not_reviewed',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($this->total === null
        || $this->reviewed === null
        || $this->pending === null
        || $this->not_reviewed === null) {
            throw new \InvalidArgumentException(
                "CardSummary expects 'total', 'reviewed', 'pending' and 'not_reviewed' attributes!"
            );
        }
    }
}
