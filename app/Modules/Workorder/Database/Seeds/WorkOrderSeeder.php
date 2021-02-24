<?php
namespace App\Modules\Workorder\Database\Seeds;

use App\Modules\Client\Models\Portfolio;
use App\Modules\Workorder\Http\Controllers\QuoteController;
use App\Modules\Workorder\Models\WorkOrder;
use App\Modules\Workorder\Models\Quote;
use App\Modules\Workorder\Models\ExpenditureType;
use App\Modules\Workorder\Models\Supplier;
use App\Modules\Property\Models\Property;
use Illuminate\Database\Seeder;

class WorkOrderSeeder extends Seeder
{
    public function run()
    {
        factory(Supplier::class, rand(1, 10))->create();
        factory(ExpenditureType::class, rand(1, 10))->create();

        factory(Quote::class, rand(150, 300))->create();

        foreach (Quote::all() as $quote) {
            factory(WorkOrder::class, rand(0, 1))->create([
                'quote_id' => $quote->id,
            ]);
        }

        foreach (WorkOrder::all() as $wo) {
            $quote = $wo->quote()->update([
                'locked_by_id' => 1,
                'locked_note' => QuoteController::AUTO_NOTE
            ]);
        }
    }
}
