<?php

namespace App\Modules\Common\Database\Seeds;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Common\Models\Note;
use App\Modules\Edits\Models\Edit;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class NoteSeeder extends Seeder
{

    /**
     * @var int
     */
    private $max = 20;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $entities = new Collection();
        $entities = $entities->merge($this->getSampleSet(ClientAccount::class));
        $entities = $entities->merge($this->getSampleSet(Property::class));
        $entities = $entities->merge($this->getSampleSet(Unit::class));
        $entities = $entities->merge($this->getSampleSet(Lease::class));
        $entities = $entities->merge($this->getSampleSet(Tenant::class));
        $entities = $entities->merge($this->getSampleSet(Edit::class));

        foreach ($entities as $entity) {
            factory(Note::class, rand(2, 5))->create([
                                                'entity_type' => get_class($entity),
                                                'entity_id'   => $entity->id,
                                            ]);
        }
    }

    /**
     * @param $model
     * @return mixed
     */
    private function getSampleSet($model)
    {

        $model = \App::make($model);

        return $model->inRandomOrder()->take($this->max)->get();
    }
}
