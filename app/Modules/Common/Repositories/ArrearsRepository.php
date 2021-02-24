<?php
namespace App\Modules\Common\Repositories;

use App\Modules\Common\Models\Arrears;
use App\Modules\Core\Classes\Repository;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\LeaseChargeType;
use Illuminate\Support\Collection;

class ArrearsRepository extends Repository implements IYardiImport
{
    public function __construct(Arrears $model)
    {
        $this->model = $model;
    }

    public function list(): Collection
    {
        return $this->model->all()->get();
    }

    public function get($id): Arrears
    {
        return $this->model->findOrFail($id);
    }

    public function importRecord(array $data)
    {
        $lease = Lease::where('cluttons_lease_ref', $data['lease_ref'])->get()[0];
        if ($lease) {
            $data['lease_id']   = $lease->id;
            $data['lease_type'] = Lease::class;
        } else {
            return;
        }

        $chargeType = LeaseChargeType::find($data['charge_type_id']);
        if (!$chargeType) {
            $data['charge_type_id'] = 9999;
        }

        $data['net']         = str_replace(' ', '', $data['net']);
        $data['vat']         = str_replace(' ', '', $data['vat']);
        $data['gross']       = str_replace(' ', '', $data['gross']);
        $data['outstanding'] = str_replace(' ', '', $data['outstanding']);
        $data['receipt']     = str_replace(' ', '', $data['receipt']);

        $data['net']         = $data['net'] === '-' ? null : $data['net'];
        $data['vat']         = $data['vat'] === '-' ? null : $data['vat'];
        $data['gross']       = $data['gross'] === '-' ? null : $data['gross'];
        $data['outstanding'] = $data['outstanding'] === '-' ? null : $data['outstanding'];
        $data['receipt']     = $data['receipt'] === '-' ? null : $data['receipt'];

        Arrears::create($data);
    }
}
