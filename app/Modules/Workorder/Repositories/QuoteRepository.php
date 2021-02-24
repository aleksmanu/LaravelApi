<?php

namespace App\Modules\Workorder\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Workorder\Models\Quote;
use App\Modules\Property\Models\Property;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Workorder\Repositories\Interfaces\IQuoteRepository;
use Illuminate\Support\Collection;

class QuoteRepository implements IQuoteRepository, IYardiImport
{
    /**
     * @var Quote
     */
    protected $model;

    /**
     * QuoteRepository constructor.
     * @param Quote $model
     */
    public function __construct(Quote $model)
    {
        $this->model = $model;
    }

    /**
     * @param bool $skip_get
     * @param array $filterData
     * @param string $sort_column
     * @param string $sort_order
     * @param integer $limit
     * @param integer $offset
     * @return Collection
     */
    public function list(
        bool $skip_get = false,
        array $filterData = [],
        string $sort_column = null,
        string $sort_order = null,
        int $limit = null,
        int $offset = null
    ) {
        if (!$sort_column && !$skip_get) {
            return $this->model->get();
        }

        $filteredResults = $this->model->newQueryWithoutRelationships()
            ->with([
                'property' => function ($query) {
                    $query->select('id', 'name', 'portfolio_id')->without('address', 'attachments', 'locationType', 'partner', 'propertyCategory', 'propertyManager', 'propertyStatus', 'propertyTenure', 'propertyUse');
                },
                'property.portfolio:id,client_account_id',
                'property.portfolio.clientAccount:id,name',
                'supplier:id,name',
                'unit:id,name'
            ])->where($filterData);

        if ($skip_get) {
            return $filteredResults;
        }
        return collect([
            'row_count' => $filteredResults->count(),
            'rows' => $filteredResults
                ->skip($offset)
                ->take($limit)
                ->orderBy($sort_column, $sort_order)
                ->get(),
        ]);
    }

    /**
     * @param int $id
     * @return Quote
     */
    public function get(int $id): Quote
    {
        return $this->model
            //    ->withTrashed()
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Quote
     */
    public function store(array $data): Quote
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Quote
     */
    public function update(int $id, array $data)
    {
        $quote = $this->get($id);
        $quote->update($data);
        return $quote;
    }

    /**
     * @param int $id
     * @return Quote
     * @throws \Exception
     */
    public function delete(int $id): Quote
    {
        $quote = $this->get($id);
        $quote->delete();
        return $quote;
    }

    /**
     * @param array $data
     * @return Quote|mixed
     */
    public function importRecord(array $data)
    {
        return $this->store($data);
    }
}
