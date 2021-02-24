<?php
namespace App\Modules\Workorder\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Workorder\Models\WorkOrder;
use App\Modules\Workorder\Models\Quote;
use App\Modules\Property\Models\Property;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Workorder\Repositories\Interfaces\IWorkOrderRepository;
use Illuminate\Support\Collection;

class WorkOrderRepository implements IWorkOrderRepository, IYardiImport
{
    /**
     * @var WorkOrder
     */
    protected $model;

    /**
     * WorkOrderRepository constructor.
     * @param WorkOrder $model
     */
    public function __construct(WorkOrder $model)
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
     * @return
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
        
        $filteredResults = $this->model
            ->join(
                Quote::getTableName(),
                WorkOrder::getTableName() . '.quote_id',
                Quote::getTableName() . '.id'
            )->join(
                Property::getTableName(),
                Quote::getTableName() . '.property_id',
                Property::getTableName() . '.id'
            )->join(
                Portfolio::getTableName(),
                Property::getTableName() . '.portfolio_id',
                Portfolio::getTableName() . '.id'
            )->where($filterData);
        
        $selectString = WorkOrder::getTableName() . '.*';
        if ($skip_get) {
            return $filteredResults;
        }
        return collect([
            'row_count' => $filteredResults->count(),
            'rows' => $filteredResults
                ->skip($offset)
                ->take($limit)
                ->orderBy($sort_column, $sort_order)
                ->select($selectString)
                ->get(),
        ]);
    }

    /**
     * @param int $id
     * @return WorkOrder
     */
    public function get(int $id): WorkOrder
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return WorkOrder
     */
    public function store(array $data): WorkOrder
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return WorkOrder
     */
    public function update(int $id, array $data): WorkOrder
    {
        $work_order = $this->get($id);
        $work_order->update($data);
        return $work_order;
    }

    /**
     * @param int $id
     * @return WorkOrder
     * @throws \Exception
     */
    public function delete(int $id): WorkOrder
    {
        $work_order = $this->get($id);
        $work_order->delete();
        return $work_order;
    }

    /**
     * @param array $data
     * @return WorkOrder|mixed
     */
    public function importRecord(array $data)
    {
        return $this->store($data);
    }

    /**
     * @param string $searchTerm
     * @return
     */
    public function search(string $searchTerm)
    {
        $wos = $this->model
            ->select([
                'work_orders.id',
                'work_orders.value',
                'suppliers.name as supplier_name',
                'work_orders.created_at',
                'work_orders.updated_at',
                \DB::raw("'Work Order' as type")
            ])->join('quotes', 'work_orders.quote_id', 'quotes.id')
            ->join('suppliers', 'quotes.supplier_id', 'suppliers.id')
            ->without('quote')
            ->where('work_orders.id', "LIKE", "%{$searchTerm}%");

        $quotes = Quote::select([
                'quotes.id',
                'quotes.value',
                'suppliers.name',
                'quotes.created_at',
                'quotes.updated_at',
                \DB::raw("'Quote' as type")
            ])->join('suppliers', 'quotes.supplier_id', 'suppliers.id')
            ->without('quote')
            ->where('quotes.id', "LIKE", "%{$searchTerm}%");
        
        return $wos->union($quotes)->orderBy('id')->get();
    }
}
