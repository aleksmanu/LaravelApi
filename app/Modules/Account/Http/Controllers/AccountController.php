<?php
namespace App\Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Http\Requests\Accounts\AccountIndexRequest;
use App\Modules\Account\Repositories\AccountRepository;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    /**
     * @var AccountRepository
     */
    protected $account_repository;

    /**
     * AccountController constructor.
     * @param AccountRepository $repository
     */
    public function __construct(AccountRepository $repository)
    {
        $this->account_repository = $repository;
    }

    /**
     * @param AccountIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(AccountIndexRequest $request)
    {
        return response($this->account_repository->getAccounts($request->account_type_id));
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
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->account_repository->getAccount($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
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
