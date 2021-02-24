<?php
namespace App\Modules\Auth\Database\Seeds;

use App\Modules\Account\Models\Account;
use App\Modules\Account\Models\AccountType;
use App\Modules\Acquisition\Models\Acquisition;
use App\Modules\Attachments\Models\Document;
use App\Modules\Attachments\Models\DocumentCategory;
use App\Modules\Attachments\Models\DocumentLevel;
use App\Modules\Attachments\Models\DocumentType;
use App\Modules\Auth\Models\Role;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\ClientAccountStatus;
use App\Modules\Client\Models\OrganisationType;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Models\Address;
use App\Modules\Common\Models\Country;
use App\Modules\Common\Models\County;
use App\Modules\Edits\Models\Edit;
use App\Modules\Edits\Models\EditBatch;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Lease\Models\BreakPartyOption;
use App\Modules\Lease\Models\LeaseType;
use App\Modules\Lease\Models\PaidStatus;
use App\Modules\Lease\Models\RentFrequency;
use App\Modules\Lease\Models\ReviewType;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\TenantStatus;
use App\Modules\Lease\Models\Transaction;
use App\Modules\Lease\Models\TransactionType;
use App\Modules\Lease\Models\LeaseCharge;
use App\Modules\Lease\Models\LeaseChargeType;
use App\Modules\Property\Models\LocationType;
use App\Modules\Property\Models\MeasurementUnit;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\PropertyCategory;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\PropertyStatus;
use App\Modules\Property\Models\PropertyUse;
use App\Modules\Property\Models\PropertyTenure;
use App\Modules\Property\Models\StopPosting;
use App\Modules\Property\Models\Unit;
use App\Modules\Auth\Models\User;
use App\Modules\Workorder\Models\ExpenditureType;
use App\Modules\Workorder\Models\Quote;
use App\Modules\Workorder\Models\WorkOrder;
use Illuminate\Database\Seeder;
use Bouncer;
use App\Modules\Auth\Models\SubRole;
use App\Modules\Workorder\Models\Supplier;
use App\Modules\Property\Models\Partner;

class RoleAndPermissionSeeder extends Seeder
{
    // All standard model operations that require permissions
    const CRUD_OPERATIONS = ['index', 'create', 'read', 'update', 'delete'];

    // The fields from `CRUD_OPERATIONS` that
    const FORBIDDEN_FOR_EDITORS = ['delete'];
    const READ_ONLY = ['index', 'read'];

    const EXT_ROLES = [Role::E_DESIGNER, Role::E_PLANNER, Role::E_SOLICITOR, Role::E_SURVEYOR];

    /**
     * List classnames of models that are part of the edit/review system
     */
    const CRUD_MODELS = [
        Address::class,
        County::class,
        Country::class,

        ClientAccount::class,
        Portfolio::class,
        Acquisition::class,

        Property::class,
        Unit::class,

        Tenant::class,
        Lease::class,
        LeaseCharge::class,

        Document::class,
    ];

    /**
     * List classnames of models that are read-only for anyone besides admins
     */
    const READ_ONLY_MODELS = [
        //User module
        User::class,

        //Client module
        Account::class,
        AccountType::class,
        ClientAccountStatus::class,
        OrganisationType::class,
        Role::class,
        SubRole::class,

        //Lease module
        BreakPartyOption::class,
        LeaseType::class,
        RentFrequency::class,
        ReviewType::class,
        TenantStatus::class,
        Transaction::class,
        TransactionType::class,
        PaidStatus::class,

        LeaseChargeType::class,

        //Property module
        LocationType::class,
        MeasurementUnit::class,
        PropertyManager::class,
        PropertyStatus::class,
        PropertyUse::class,
        PropertyTenure::class,
        PropertyCategory::class,
        StopPosting::class,
        EditBatchType::class,
        Partner::class,

        //Document module
        DocumentCategory::class,
        DocumentType::class,
        DocumentLevel::class,

        //Work Order module
        Supplier::class,
        ExpenditureType::class,
        Quote::class,
        WorkOrder::class
    ];

    /**
     * Run the datamodels
     *
     * @return voidmodels
     */
    public function run()
    {
        Bouncer::allow(Role::DEVELOPER)->everything();

        Role::whereIn('name', [Role::DEVELOPER, Role::ADMIN, Role::AUTHORISER, Role::EDITOR])
            ->update(['is_system' => true]);

        $this->setAdminRole();
        $this->setAuthoriserRole();
        $this->setClientRole();
        $this->setEditorRole();
        $this->setEditPermissions();
        $this->setExternalPermissions();
    }

    private function setClientRole()
    {
        foreach (self::CRUD_MODELS as $className) {
            Bouncer::allow(Role::CLIENT)->to(self::READ_ONLY, $className);
        }
        foreach (self::READ_ONLY_MODELS as $className) {
            Bouncer::allow(Role::CLIENT)->to(self::READ_ONLY, $className);
        }
    }

    private function setAdminRole()
    {
        foreach (self::CRUD_MODELS as $className) {
            Bouncer::allow(Role::ADMIN)->to(self::CRUD_OPERATIONS, $className);
        }
        foreach (self::READ_ONLY_MODELS as $className) {
            Bouncer::allow(Role::ADMIN)->to(self::CRUD_OPERATIONS, $className);
        }
        Bouncer::allow(Role::ADMIN)->to(self::CRUD_OPERATIONS, Acquisition::class);
    }

    private function setAuthoriserRole()
    {
        foreach (self::CRUD_MODELS as $className) {
            Bouncer::allow(Role::AUTHORISER)->to(self::CRUD_OPERATIONS, $className);
            Bouncer::disallow(Role::AUTHORISER)->to(['delete'], $className);
        }
        foreach (self::READ_ONLY_MODELS as $className) {
            Bouncer::allow(Role::AUTHORISER)->to(self::READ_ONLY, $className);
        }
    }

    private function setEditorRole()
    {
        foreach (self::CRUD_MODELS as $className) {
            Bouncer::allow(Role::EDITOR)->to(self::CRUD_OPERATIONS, $className);
            Bouncer::disallow(Role::EDITOR)->to(self::FORBIDDEN_FOR_EDITORS, $className);
        }
        foreach (self::READ_ONLY_MODELS as $className) {
            Bouncer::allow(Role::EDITOR)->to(self::READ_ONLY, $className);
        }
    }

    private function setEditPermissions()
    {
        Bouncer::allow(Role::AUTHORISER)->to(self::CRUD_OPERATIONS, Edit::class);
        Bouncer::allow(Role::AUTHORISER)->to(self::CRUD_OPERATIONS, EditBatch::class);
    }

    private function setExternalPermissions()
    {
        foreach (RoleAndPermissionSeeder::EXT_ROLES as $externalRole) {
            Bouncer::allow($externalRole)->to(self::CRUD_OPERATIONS, Acquisition::class);
        }
    }
}
