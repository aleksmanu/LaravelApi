<?php

namespace App\Modules\Client\Observers;

use App\Modules\Account\Repositories\AccountRepository;
use App\Modules\Client\Models\ClientAccount;

class ClientAccountObserver
{

    /**
     * Handle the client account "created" event.
     *
     * @param  ClientAccount $clientAccount
     * @return void
     */
    public function created(ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Handle the client account "updated" event.
     *
     * @param  ClientAccount $client_account
     * @return void
     */
    public function updated(ClientAccount $client_account)
    {
        $account = $client_account->account;

        if ($account->name !== $client_account->name) {
            $repository = \App::make(AccountRepository::class);
            $repository->updateAccount($account->id, ['name' => $client_account->name]);
        }
    }

    /**
     * Handle the client account "deleted" event.
     *
     * @param  ClientAccount $clientAccount
     * @return void
     */
    public function deleted(ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Handle the client account "restored" event.
     *
     * @param  ClientAccount $clientAccount
     * @return void
     */
    public function restored(ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Handle the client account "force deleted" event.
     *
     * @param  ClientAccount $clientAccount
     * @return void
     */
    public function forceDeleted(ClientAccount $clientAccount)
    {
        //
    }
}
