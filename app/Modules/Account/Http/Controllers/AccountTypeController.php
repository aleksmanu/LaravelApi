<?php
namespace App\Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Http\Requests\AccountTypes\AccountTypeIndexRequest;
use App\Modules\Account\Models\AccountType;
use App\Modules\Account\Repositories\AccountTypeRepository;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{

    /**
     * @var AccountTypeRepository
     */
    protected $account_type_repository;

    /**
     * AccountTypeController constructor.
     * @param AccountTypeRepository $repository
     */
    public function __construct(AccountTypeRepository $repository)
    {
        $this->account_type_repository = $repository;
    }

    /**
     * @param AccountTypeIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(AccountTypeIndexRequest $request)
    {
        return response($this->account_type_repository->getAccountTypes());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // TODO: Figure out what this is and build it
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AccountType $accountType)
    {
        // TODO: Figure out what this is and build it
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // TODO: Figure out what this is and build it
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // TODO: Figure out what this is and build it
    }
}
