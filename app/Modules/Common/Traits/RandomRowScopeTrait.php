<?php
namespace App\Modules\Common\Traits;

trait RandomRowScopeTrait
{
    /**
     * Distribution is a little skewed but it's better than inRandomOrder() trash
     *
     * @param $query
     * @return mixed
     */
    public function scopeRandomRow($query)
    {
        if ($query->toSql() !== $this->newQuery()->toSql()) {
            throw new \BadFunctionCallException(
                'randomRow() scope should only be applied to base queries (no preconditions!).'
            );
        }

        $first_id = $this->newQuery()->first()->id;
        $last_id = $this->newQuery()->orderBy($this->getTableName() . '.id', 'desc')->first()->id;
        return $query->where('id', '>=', rand($first_id, $last_id))->first();
    }
}
