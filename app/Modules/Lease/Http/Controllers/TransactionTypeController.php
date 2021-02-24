<?php

namespace App\Modules\Lease\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lease\Http\Requests\TransactionTypes\TransactionTypeStoreRequest;
use App\Modules\Lease\Http\Requests\TransactionTypes\TransactionTypeUpdateRequest;
use App\Modules\Lease\Repositories\TransactionTypeRepository;

class TransactionTypeController extends Controller
{
    protected $transaction_type_repository;

    public function __construct(TransactionTypeRepository $transactionTypeRepository)
    {
        $this->transaction_type_repository = $transactionTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->transaction_type_repository->getTransactionTypes());
    }

    /**
     * @param TransactionTypeStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(TransactionTypeStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->transaction_type_repository->storeTransactionType($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->transaction_type_repository->getTransactionType($id));
    }

    /**
     * @param TransactionTypeUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(TransactionTypeUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();
        return response($this->transaction_type_repository->updateTransactionType($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->transaction_type_repository->deleteTransactionType($id));
    }
}
