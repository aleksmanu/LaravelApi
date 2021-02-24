<?php
namespace App\Modules\Client\Database\Seeds;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\ClientAccountStatus;
use App\Modules\Client\Models\OrganisationType;
use App\Modules\Client\Models\Portfolio;
use Illuminate\Database\Seeder;

class ClientDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(
            OrganisationType::class,
            rand(5 + getModelMagnitudeFactor(), 10 + getModelMagnitudeFactor())
        )->create();
        factory(
            ClientAccountStatus::class,
            rand(5 + getModelMagnitudeFactor(), 10 + getModelMagnitudeFactor())
        )->create();
        factory(
            ClientAccount::class,
            rand(2 * getModelMagnitudeFactor(), 5 * getModelMagnitudeFactor())
        )->create();

        foreach (ClientAccount::all() as $clientAccount) {
            factory(Portfolio::class, rand(2 * getModelMagnitudeFactor(), 4 * getModelMagnitudeFactor()))->create([
                'client_account_id' => $clientAccount->id,
            ]);
        }
    }
}
