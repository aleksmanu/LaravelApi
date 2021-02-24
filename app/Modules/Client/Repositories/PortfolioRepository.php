<?php

namespace App\Modules\Client\Repositories;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Core\Classes\Repository;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Common\Traits\HasServerSideSortingTrait;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\ReviewStatus;
use Illuminate\Support\Collection;

class PortfolioRepository extends Repository implements IYardiImport
{
    use HasServerSideSortingTrait;

    /**
     * ClientAccountRepository constructor.
     * @param Portfolio $model
     */
    public function __construct(Portfolio $model)
    {
        $this->model = $model;
    }

    /**
     * @param $sort_column
     * @param $sort_order
     * @param $offset
     * @param $limit
     * @param $client_account_id
     * @param $portfolio_name_partial
     * @return Collection
     */
    public function getPortfolioDataTable(
        $sort_column,
        $sort_order,
        $offset,
        $limit,
        $client_account_id,
        $portfolio_name_partial
    ): Collection {
        $query = $this->model->newQueryWithoutRelationships()->select($this->model->getTableName() . '.id', $this->model->getTableName() . '.name', $this->model->getTableName() . '.yardi_portfolio_ref', $this->model->getTableName() . '.client_account_id');
        $query->withCount($this->model->withCount);
        $query->with([
            'clientAccount:id,name'
        ]);

        $query->groupBy('portfolios.id');
        $query = $this->applyFilters($query, $client_account_id, $portfolio_name_partial);

        // Get a copy of the query at this point with no limits applied, otherwise total count will be skewed
        // Insert the monster query inside a simple count() query. Replace bindings first
        $countSelect = 'SELECT count(*) as row_count FROM (' . $query->toSql() . ') AS T1';
        $bindParams  = $query->getBindings(); //keep a hold of these before sort and limit is added

        $this->johnifySortColumn($sort_column);

        $query->skip($offset)
            ->take($limit)
            ->orderBy($sort_column, $sort_order);


        return collect([
            'row_count' => \DB::select($countSelect, $bindParams)[0]->row_count,
            'rows'      => $query->get()
        ]);
    }

    /**
     * @return Collection
     */
    public function getPortfolios(): Collection
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->withCount($this->model->withCount)
            ->orderBy($this->model->getTableName() . '.name', 'asc')->get();
    }

    /**
     * @param array $params
     * @return Collection
     */
    public function identifyPortfolios(array $params): Collection
    {
        $query = $this->model
            ->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.id');

        if (isset($params['portfolio_id'])) {
            $params['id'] = $params['portfolio_id'];
        }

        foreach ($params as $key => $val) {
            if ($key === "id" || in_array($key, $this->model->fillable)) {
                $query->where($this->model->getTableName() . '.' . $key, $val);
            }
        }

        return $query->get()->pluck('id');
    }

    /**
     * @param array $ids
     * @return Collection
     */
    public function findPortfolios(array $ids): Collection
    {
        return $this->model
            ->select($this->model->getTableName() . '.id', $this->model->getTableName() . '.client_account_id', $this->model->getTableName() . '.name', $this->model->getTableName() . '.yardi_portfolio_ref')
            ->with(['clientAccount:id,name'])
            ->withCount($this->model->withCount)
            ->whereIn($this->model->getTableName() . '.id', $ids)
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->get();
    }

    /**
     * get portfolios data by valid data parameters.
     *
     * @param array $params
     * @return Collection
     */
    public function findPortfoliosByValidData(array &$params): Collection
    {
        if (isset($params['portfolio_id'])) {
            $params['id'] = $params['portfolio_id'];
        }

        return $this->model->with('reviewStatus', 'lockedByUser', 'clientAccount')
            ->withCount($this->model->withCount)
            ->whereIn($this->model->getTableName() . '.id', function ($q) use ($params) {
                $q->select('id')
                    ->from($this->model->getTableName());

                foreach ($params as $key => $val) {
                    if ($key === "id" || in_array($key, $this->model->fillable)) {
                        $q->where($this->model->getTableName() . '.' . $key, $val);
                    }
                }
            })
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->get();
    }

    /**
     * @param int $id
     * @return Portfolio
     */
    public function getPortfolio(int $id): Portfolio
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->with([
                'clientAccount',
                'reviewStatus',
                'lockedByUser'
            ])
            ->withCount($this->model->withCount)
            ->findOrFail($id);
    }

    public function storePortfolio(array $data): Portfolio
    {

        $data['review_status_id'] = EloquentHelper::getRecordIdBySlug(
            ReviewStatus::class,
            ReviewStatus::NEVER_REVIEWED
        );
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Portfolio
     */
    public function updatePortfolio(int $id, array $data): Portfolio
    {
        $portfolio = $this->getPortfolio($id);
        $portfolio->update($data);
        return $portfolio;
    }

    /**
     * @param int $id
     * @return Portfolio
     * @throws \Exception
     */
    public function deletePortfolio(int $id): Portfolio
    {
        $portfolio = $this->getPortfolio($id);
        $portfolio->delete();
        return $portfolio;
    }

    /**
     * @param $base_query
     * @param int $client_account_id
     * @param string $portfolio_name_partial
     * @return mixed
     */
    private function applyFilters($base_query, int $client_account_id, $portfolio_name_partial = '')
    {
        if ($client_account_id) {
            $base_query->where($this->model->getTableName() . '.client_account_id', $client_account_id);
        }

        if ($portfolio_name_partial) {
            $base_query->where('portfolios.name', 'LIKE', '%' . $portfolio_name_partial . '%');
        }

        return $base_query;
    }

    /**
     * @param array $data
     * @return Portfolio|mixed
     */
    public function importRecord(array $data)
    {

        $client_account = ClientAccount::where('yardi_client_ref', $data['yardi_client_ref'])->first();
        $portfolio = $this->identifyPortfolios([
            'yardi_portfolio_ref' => $data['yardi_portfolio_ref']
        ])->first();
        if ($portfolio) {
            $portfolio = $this->getPortfolio($portfolio);
            return $portfolio->update([
                'client_account_id' => $client_account->id,
                'name' => $data['name'],
            ]);
        } else {
            return $this->storePortfolio([
                'client_account_id' => $client_account->id,
                'name' => $data['name'],
                'yardi_portfolio_ref' => $data['yardi_portfolio_ref'],
            ]);
        }
    }
}
