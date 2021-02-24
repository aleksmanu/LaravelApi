<?php

namespace App\Modules\Edits\Database\Seeds;

use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Models\Address;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Core\Library\StringHelper;
use App\Modules\Edits\Helpers\EditBatchEntityHelper;
use App\Modules\Edits\Helpers\SeedHelper;
use App\Modules\Edits\Models\Edit;
use App\Modules\Edits\Models\EditBatch;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Edits\Models\EditStatus;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class EditsSeeder extends Seeder
{

    /**
     * @var Factory
     */
    private $faker;

    /**
     * @var int
     */
    private $max = 50;

    /**
     * EditsSeeder constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create('en_GB');
    }

    /**
     * @return bool
     */
    public function run()
    {

        $locked_clients   = SeedHelper::getSampleEntitySet(ClientAccount::class, $this->max, ReviewStatus::IN_REVIEW);
        $reviewed_clients = SeedHelper::getSampleEntitySet(ClientAccount::class, $this->max, ReviewStatus::REVIEWED);

        $locked_portfolios   = SeedHelper::getSampleEntitySet(Portfolio::class, $this->max, ReviewStatus::IN_REVIEW);
        $reviewed_portfolios = SeedHelper::getSampleEntitySet(Portfolio::class, $this->max, ReviewStatus::REVIEWED);

        $locked_properties   = SeedHelper::getSampleEntitySet(Property::class, $this->max, ReviewStatus::IN_REVIEW);
        $reviewed_properties = SeedHelper::getSampleEntitySet(Property::class, $this->max, ReviewStatus::REVIEWED);

        $locked_units   = SeedHelper::getSampleEntitySet(Unit::class, $this->max, ReviewStatus::IN_REVIEW);
        $reviewed_units = SeedHelper::getSampleEntitySet(Unit::class, $this->max, ReviewStatus::REVIEWED);

        $locked_leases   = SeedHelper::getSampleEntitySet(Lease::class, $this->max, ReviewStatus::IN_REVIEW);
        $reviewed_leases = SeedHelper::getSampleEntitySet(Lease::class, $this->max, ReviewStatus::REVIEWED);

        $locked_tenants   = SeedHelper::getSampleEntitySet(Tenant::class, $this->max, ReviewStatus::IN_REVIEW);
        $reviewed_tenants = SeedHelper::getSampleEntitySet(Tenant::class, $this->max, ReviewStatus::REVIEWED);

        $locked_addresses   = SeedHelper::getSampleEntitySet(Address::class, $this->max, ReviewStatus::IN_REVIEW);
        $reviewed_addresses = SeedHelper::getSampleEntitySet(Address::class, $this->max, ReviewStatus::REVIEWED);

        $this->createBatches($locked_clients, false);
        $this->createBatches($reviewed_clients, true);

        $this->createBatches($locked_portfolios, false);
        $this->createBatches($reviewed_portfolios, true);

        $this->createBatches($locked_properties, false);
        $this->createBatches($reviewed_properties, true);

        $this->createBatches($locked_units, false);
        $this->createBatches($reviewed_units, true);

        $this->createBatches($locked_leases, false);
        $this->createBatches($reviewed_leases, true);

        $this->createBatches($locked_tenants, false);
        $this->createBatches($reviewed_tenants, true);

        $this->createBatches($locked_addresses, false);
        $this->createBatches($reviewed_addresses, true);

        return true;
    }

    /**
     * @param $entities
     * @param $completed
     */
    private function createBatches($entities, $completed)
    {

        foreach ($entities as $entity) {
            $this->createEditBatch($entity, $completed);
        }
    }

    /**
     * @param $entity
     * @param $completed
     * @return string
     */
    private function createEditBatch($entity, $completed)
    {

        $class = get_class($entity);

        $created_user     = $completed ? User::inRandomOrder()->first()->id : $entity->lockedByUser->id;
        $reviewed_by_user = $completed ? User::first()->id : null;
        $created_at       = $completed ? Carbon::now()->subWeek(rand(1, $this->max)) : $entity->locked_at;
        $reviewed_at      = $completed ? $created_at->copy()->addWeek() : null;
        $edit_batch_type  = EditBatchType::inRandomOrder()->first()->id;

        if ($class === Unit::class) {
            $name = $entity->demise;
        } elseif ($class === Lease::class) {
            $name = $entity->tenants()->first()->name; //TODO - check if there is a way to get current active tenant
        } elseif ($class === Address::class) {
            $name = 'Address: ' . $entity->number . ' ' . $entity->street . ' ' . $entity->postcode;
        } else {
            $name = $entity->name;
        }

        $data = [
            'entity_type'         => $class,
            'entity_id'           => $entity->id,
            'created_by_user_id'  => $created_user,
            'name'                => $name,
            'created_at'          => $created_at,
            'reviewed_at'         => $reviewed_at,
            'status_changed_at'   => $created_at,
            'reviewed_by_user_id' => $reviewed_by_user,
            'edit_batch_type_id'  => $edit_batch_type,
        ];
        $batch = factory(EditBatch::class, 1)->create($data);

        $this->createEdit($batch[0], $entity, $completed);
    }

    /**
     * @param EditBatch $batch
     * @param $entity
     * @param $completed
     */
    private function createEdit(EditBatch $batch, $entity, $completed)
    {

        $attributes = SeedHelper::removeUtilityFields($entity->getFillable());

        foreach ($attributes as $attribute) {
            $edit_data = [
                'edit_batch_id' => $batch->id,
                'field'         => $attribute,
            ];

            $new_field = SeedHelper::getValueBasedOnFieldType($entity::getTableName(), $attribute, $this->faker);

            $edit_data['previous_value'] = $completed ? $new_field : $entity->$attribute;
            $edit_data['value']          = $completed ? $entity->$attribute : $new_field;

            $check_str = substr($attribute, -3);
            if ($check_str === '_id') {
                $class                       = StringHelper::snakeCaseToCamelCase(rtrim($attribute, '_id'));
                $edit_data['foreign_entity'] = get_class($entity->$class);
            }
            SeedHelper::setRandomEditStatus($edit_data, $completed);

            \factory(Edit::class, 1)->create($edit_data);
        }
    }
}
