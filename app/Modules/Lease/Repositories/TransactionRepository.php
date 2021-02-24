<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Common\Traits\HasServerSideSortingTrait;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\LeaseChargeType;
use App\Modules\Lease\Models\PaidStatus;
use App\Modules\Lease\Models\Transaction;
use App\Modules\Lease\Models\TransactionType;
use App\Modules\Property\Models\Unit;
use Illuminate\Support\Collection;

class TransactionRepository implements IYardiImport
{
    use HasServerSideSortingTrait;

    /**
     * @var Transaction
     */
    protected $model;

    /**
     * TransactionRepository constructor.
     * @param Transaction $model
     */
    public function __construct(Transaction $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getTransactions(): Collection
    {
        return $this->model
            ->limit(config('misc.api.maximumResponseSize'))
            ->orderBy('id', 'desc')->get();
    }

    /**
     * @param $id
     * @return Transaction
     */
    public function getTransaction(int $id): Transaction
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Transaction
     */
    public function store(array $data): Transaction
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Transaction
     */
    public function updateTransaction(int $id, array $data): Transaction
    {
        $Transaction = $this->getTransaction($id);
        $Transaction->update($data);
        return $Transaction;
    }

    /**
     * @param int $id
     * @return Transaction
     * @throws \Exception
     */
    public function deleteTransaction(int $id): Transaction
    {
        $Transaction = $this->getTransaction($id);
        $Transaction->delete();
        return $Transaction;
    }

    public function getUnitTransactionsDataTable(
        string $sort_column,
        string $sort_order,
        int $offset,
        int $limit,
        int $unit_id,
        $search_key
    ): Collection {
        $select = Transaction::getTableName() . '.*';
        $query = $this->model->selectRaw($select);

        $query->leftJoin(
            LeaseChargeType::getTableName(),
            LeaseChargeType::getTableName() . '.id',
            '=',
            Transaction::getTableName() . '.lease_charge_type_id'
        );
        $query->join(
            Lease::getTableName(),
            Lease::getTableName() . '.id',
            '=',
            Transaction::getTableName() . '.lease_id'
        );
        $query->join(
            Unit::getTableName(),
            Unit::getTableName() . '.id',
            '=',
            Lease::getTableName() . '.unit_id'
        );

        $query->with([
            'leaseChargeType',
            'paidStatus',
        ])->groupBy(Transaction::getTableName() . '.id');


        if ($unit_id) {
            $query->where(Unit::getTableName() . '.id', $unit_id);
        }

        if ($search_key) {
            $query->where(function ($query) use ($search_key) {
                $query->where(Transaction::getTableName() . '.invoice_number', 'LIKE', '%'.$search_key.'%')
                    ->orWhere(TransactionType::getTableName() . '.name', 'LIKE', '%'.$search_key.'%');
            });
        }

        // Get a copy of the query at this point with no limits applied, otherwise total count will be skewed
        // Insert the monster query inside a simple count() query. Replace bindings first
        $countSelect = 'SELECT count(*) as row_count FROM (' . $query->toSql() . ') AS T1';
        $bindParams = $query->getBindings(); //keep a hold of these before sort and limit is added

        $this->johnifySortColumn($sort_column);

        $query->skip($offset)
            ->take($limit)
            ->orderBy($sort_column, $sort_order);

        if (!$limit) {
            $query->limit(config('misc.api.maximumResponseSize'));
        }

        return collect([
            'row_count' => \DB::select($countSelect, $bindParams)[0]->row_count,
            'rows' => $query->get()
        ]);
    }

    /**
     * @param array $data
     * @return bool|mixed
     */
    public function importRecord(array $data)
    {
        $lease = Lease::where('cluttons_lease_ref', $data['lease_ref'])->first();
        $data['lease_type'] = Lease::class;
        if (!$lease) {
            return;
        }
        $data['lease_id'] = $lease->id;
        $data['lease_charge_type_id'] = $data['charge_type_id'];
        
        $data['paid_at'] = $data['paid_at'] === 'Unpaid' ? null : $data['paid_at'];
        $data['paid_status_id'] = PaidStatus::where('name', $data['paid_status'])->first()->id;

        return $this->store($data);
    }
}
