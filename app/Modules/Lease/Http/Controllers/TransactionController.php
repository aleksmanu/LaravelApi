<?php
namespace App\Modules\Lease\Http\Controllers;

use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Modules\Lease\Http\Requests\Transactions\TransactionDataTableRequest;
use App\Modules\Lease\Http\Requests\Transactions\TransactionDeleteRequest;
use App\Modules\Lease\Http\Requests\Transactions\TransactionStoreRequest;
use App\Modules\Lease\Http\Requests\Transactions\TransactionUpdateRequest;
use App\Modules\Lease\Repositories\TransactionRepository;

class TransactionController extends Controller
{
    protected $transaction_repository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transaction_repository = $transactionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->transaction_repository->getTransactions());
    }

    /**
     * @param TransactionStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(TransactionStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->transaction_repository->storeTransaction($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->transaction_repository->getTransaction($id));
    }

    /**
     * @param TransactionUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(TransactionUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();
        return response($this->transaction_repository->updateTransaction($id, $validated_data));
    }

    /**
     * @param TransactionDeleteRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(TransactionDeleteRequest $request, int $id)
    {
        return response($this->transaction_repository->deleteTransaction($id));
    }

    /**
     * @param TransactionDataTableRequest $request
     * @return Collection
     */
    public function unitTransactionsDataTable(TransactionDataTableRequest $request)
    {
        return response($this->transaction_repository->getUnitTransactionsDataTable(
            $request->sort_column ?? config('misc.dataTable.defaultSortColumn'),
            $request->sort_order ?? config('misc.dataTable.defaultSortOrder'),
            intval($request->offset),
            $request->limit ?? config('misc.dataTable.defaultPerPage'),
            intval($request->unit_id),
            $request->search_key
        ));
    }
}
