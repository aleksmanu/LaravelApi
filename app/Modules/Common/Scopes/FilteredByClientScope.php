<?php
namespace App\Scopes;

use App\Modules\Account\Models\Account;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FilteredByClientScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * https://i.redd.it/lofsyr4oq8h21.png
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (auth()->check() && auth()->payload()->get('restriction_account_id')) {
            // This will duplicate the base model's table as clientFilter_{getTableName()}
            $builder->join(
                $model->getTableName() . ' AS clientFilter_' . $model->getTableName(),
                'clientFilter_' . $model->getTableName() . '.id',
                '=',
                $model->getTableName() . '.id'
            );

            foreach ($model->getJoinPathToAccount() as $joinDetails) {
                $foreignColumn = array_key_exists(2, $joinDetails) ? $joinDetails[2] : 'id';

                $builder->join(
                    $joinDetails[0] . ' AS clientFilter_' . $joinDetails[0],
                    'clientFilter_' . $joinDetails[0] . '.' . $foreignColumn,
                    '=',
                    'clientFilter_' . $joinDetails[1]
                );
            }

            $builder->where(
                'clientFilter_' . Account::getTableName() . '.id',
                '=',
                auth()->payload()->get('restriction_account_id')
            );
        }
    }
}
