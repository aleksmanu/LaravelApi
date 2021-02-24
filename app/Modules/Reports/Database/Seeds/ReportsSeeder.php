<?php
namespace App\Modules\Reports\Database\Seeds;

use Illuminate\Database\Seeder;
use App\Modules\Reports\Models\Report;
use App\Modules\Reports\Models\ReportColumn;

class ReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->reports as $reportData) {
            $report         = new Report();
            $report->name   = $reportData[0];
            $report->slug   = $reportData[1];
            $report->source = $reportData[2];
            $report->save();

            foreach ($this->columns[$report->slug] as $columnData) {
                $column              = new ReportColumn();
                $column->report_id   = $report->id;
                $column->preview     = $columnData[0];
                $column->name        = $columnData[1];
                $column->attribute   = $columnData[2];
                $column->arrangement = $columnData[3];
                $column->save();
            }
        }
    }

    private $reports = [
        ['Property Report', 'property_report', 'property'],
        ['Unit Report', 'unit_report', 'unit'],
        ['Lease Payable Report - All Event Dates', 'lease_payable_all_event_dates', 'lease_payable_all'],
        ['Lease Payable Report - Breaks', 'lease_payable_all_breaks', 'lease_payable_break'],
        ['Lease Payable Report - Rent Review', 'lease_payable_rent_review', 'lease_payable_rent'],
        ['Lease Payable Report - Expiry Dates', 'lease_payable_expiry_dates', 'lease_payable_expiry'],
    ];

    private $columns = [
        'property_report' => [
            [true, 'Client Ref', 'client_accounts.yardi_client_ref', 0],
            [true, 'Client Name', 'client_accounts.name', 1],
            [true, 'Portfolio Ref', 'portfolios.yardi_portfolio_ref', 2],
            [true, 'Portfolio Name', 'portfolios.name', 3],
            [true, 'Yardi Property Ref', 'properties.yardi_property_ref', 4],
            [true, 'Client Property Ref', 'properties.yardi_alt_ref', 5],
            [true, 'Property Name', 'properties.name', 6],
            [
                false,
                'Property Address',
                "CONCAT_WS(
                    ' ',
                    addresses.unit,
                    addresses.number,
                    addresses.building,
                    addresses.street,
                    addresses.town,
                    addresses.postcode,
                    counties.name,
                    countries.name
                )",
                7
            ],
            [false, 'Address 1', 'addresses.unit', 8],
            [false, 'Address 2', "CONCAT_WS(' ', addresses.number, addresses.building)", 9],
            [false, 'Address 3', 'addresses.street', 10],
            [false, 'Town', 'addresses.town', 11],
            [false, 'County', 'counties.name', 12],
            [false, 'Country', 'countries.name', 13],
            [false, 'Postcode', 'addresses.postcode', 14],
            [false, 'Lat', 'addresses.latitude', 15],
            [false, 'Long', 'addresses.longitude', 16],
            [false, 'Tenure', 'property_tenures.name', 17],
            [false, 'Main Use', 'property_uses.name', 18],
            [
                false,
                'Cluttons Property Manager Name',
                "CONCAT_WS(
                    ' ',
                    property_manager_user.first_name,
                    property_manager_user.last_name
                )",
                19
            ],
            [false, 'Cluttons Property Manager Email', 'property_manager_user.email', 20],
            [false, 'Cluttons Property Manager Telephone', "'-'", 21],
        ],
        'unit_report' => [
            [true, 'Client Ref', 'client_accounts.yardi_client_ref', 0],
            [true, 'Client Name', 'client_accounts.name', 1],
            [false, 'Portfolio Ref', 'portfolios.yardi_portfolio_ref', 2],
            [true, 'Portfolio Name', 'portfolios.name', 3],
            [false, 'Yardi Property Ref', 'properties.yardi_property_ref', 4],
            [false, 'Client Property Ref', 'properties.yardi_alt_ref', 5],
            [true, 'Property Name', 'properties.name', 6],
            [
                false,
                'Property Address',
                "CONCAT_WS(
                    ' ',
                    addresses.unit,
                    addresses.number,
                    addresses.building,
                    addresses.street,
                    addresses.town,
                    addresses.postcode,
                    counties.name,
                    countries.name
                )",
                7
            ],
            [false, 'Address 1', 'addresses.unit', 8],
            [false, 'Address 2', "CONCAT_WS(' ', addresses.number, addresses.building)", 9],
            [false, 'Address 3', 'addresses.street', 10],
            [false, 'Town', 'addresses.town', 11],
            [false, 'County', 'counties.name', 12],
            [false, 'Country', 'countries.name', 13],
            [false, 'Postcode', 'addresses.postcode', 14],
            [false, 'Lat', 'addresses.latitude', 15],
            [false, 'Long', 'addresses.longitude', 16],
            [false, 'Tenure', 'property_tenures.name', 17],
            [true, 'Demise', 'units.demise', 18],
            [true, 'Unit Ref', 'units.yardi_import_ref', 19],
            [false, 'Main Use', 'property_uses.name', 20],
            [false, 'Lettable Area', '""', 21],
            [false, 'ITZA', '""', 22],
            [false, 'Site Area', '""', 23],
            [false, 'Measurement Type', '""', 24],
            [false, 'Gross Internal Area', '""', 25],
            [false, 'Non Lettable', '""', 26],
            [false, 'Unit Type', '""', 27],
            [false, 'Status', '""', 28],
            [false, 'VOID', '""', 29],
            [false, 'Tenant Name', '""', 30],
            [false, 'Passing Rent', '""', 31],
            [false, 'Void From', '""', 32],
            [
                false,
                'Cluttons Property Manager Name',
                "CONCAT_WS(
                    ' ',
                    property_manager_user.first_name,
                    property_manager_user.last_name
                )",
                33
            ],
            [false, 'Cluttons Property Manager Email', 'property_manager_user.email', 34],
            [false, 'Cluttons Property Manager Telephone', "''", 35],
        ],
        'lease_payable_all_event_dates' => [
            [false, 'Client Ref', 'client_accounts.yardi_client_ref', 0],
            [true, 'Client Name', 'client_accounts.name', 1],
            [false, 'Portfolio Ref', 'portfolios.yardi_portfolio_ref', 2],
            [false, 'Portfolio Name', 'portfolios.name', 3],
            [false, 'Yardi Property Ref', 'properties.yardi_property_ref', 4],
            [false, 'Client Property Ref', 'properties.yardi_alt_ref', 5],
            [true, 'Property Name', 'properties.name', 6],
            [
                false,
                'Property Address',
                "CONCAT_WS(
                    ' ',
                    addresses.unit,
                    addresses.number,
                    addresses.building,
                    addresses.street,
                    addresses.town,
                    addresses.postcode,
                    counties.name,
                    countries.name
                )",
                7
            ],
            [false, 'Property Tenure', 'property_tenures.name', 8],
            [false, 'Property Main Use', 'property_uses.name', 9],
            [true, 'Yardi Lease Ref', 'lease_payables.cluttons_lease_ref', 10],
            [true, 'Alternate Lease Ref', 'lease_payables.client_lease_ref', 11],
            [false, 'Lease Description', '""', 12],
            [false, 'Term (Years)', '""', 13],
            [false, 'Term (Months)', '""', 14],
            [false, 'Term (Days)', '""', 15],
            [false, 'Date of Agreement', 'date_format(lease_payables.agreement_date, "%d/%m/%Y")', 16],
            [false, 'Lease Start Date', 'date_format(lease_payables.lease_start, "%d/%m/%Y")', 17],
            [false, 'Lease End Date', 'date_format(lease_payables.lease_end, "%d/%m/%Y")', 18],
            [false, 'Landlord Name', 'landlord.name', 19],
            [false, 'Managing Agent Name', 'managing_agent.name', 20],
            [false, 'Passing Rent', 'sum(lease_rent.annual)', 21],
            [false, 'Passing Rent Frequency', 'lease_rent.frequency', 22],
            [false, 'Service Charge', 'sum(lease_service.annual)', 23],
            [false, 'Insurance Charge', 'sum(lease_insurance.annual)', 24],
            [false, 'Rent Review Pattern', 'lease_payables.review_pattern', 25],
            [false, 'Next Review Date', 'date_format(lease_payables.next_review, "%d/%m/%Y")', 26],
            [false, 'Rent Review Basis', 'lease_payables.review_basis', 27],
            [false, 'Break Option', 'break_party_options.name', 28],
            [false, 'Next Break Date', 'lease_payables.next_break_date', 30],
            [false, 'Break Min Notice Periods (months)', '""', 31],
            [false, 'Break Penalty', 'lease_breaks.penalty', 32],
            [false, 'Break Penalty/Incentive', 'lease_breaks.penalty_incentive', 33],
            [false, 'Break Penalty Notes', 'lease_breaks.notes', 34],
            [false, 'Break Notice Served?', '""', 35],
            [false, 'Break Effective From', '""', 36],
            [false, 'L&T 1954 Exclusion', 'lease_payables.outside_54_act', 37],
            [
                false,
                'Cluttons Property Manager Name',
                "CONCAT_WS(
                    ' ',
                    property_manager_user.first_name,
                    property_manager_user.last_name
                )",
                38
            ],
            [false, 'Cluttons Property Manager Email', 'property_manager_user.email', 39],
            [false, 'Cluttons Property Manager Telephone', "'-'", 40],
        ],
        'lease_payable_all_breaks' => [
            [false, 'Client Ref', 'client_accounts.yardi_client_ref', 0],
            [true, 'Client Name', 'client_accounts.name', 1],
            [false, 'Portfolio Ref', 'portfolios.yardi_portfolio_ref', 2],
            [false, 'Portfolio Name', 'portfolios.name', 3],
            [false, 'Yardi Property Ref', 'properties.yardi_property_ref', 4],
            [false, 'Client Property Ref', 'properties.yardi_alt_ref', 5],
            [true, 'Property Name', 'properties.name', 6],
            [
                false,
                'Property Address',
                "CONCAT_WS(
                    ' ',
                    addresses.unit,
                    addresses.number,
                    addresses.building,
                    addresses.street,
                    addresses.town,
                    addresses.postcode,
                    counties.name,
                    countries.name
                )",
                7
            ],
            [false, 'Property Tenure', 'property_tenures.name', 8],
            [false, 'Property Main Use', 'property_uses.name', 9],
            [true, 'Yardi Lease Ref', 'lease_payables.cluttons_lease_ref', 10],
            [true, 'Alternate Lease Ref', 'lease_payables.client_lease_ref', 11],
            [false, 'Lease Description', '""', 12],
            [false, 'Term (Years)', '""', 13],
            [false, 'Term (Months)', '""', 14],
            [false, 'Term (Days)', '""', 15],
            [false, 'Date of Agreement', 'date_format(lease_payables.agreement_date, "%d/%m/%Y")', 16],
            [false, 'Lease Start Date', 'date_format(lease_payables.lease_start, "%d/%m/%Y")', 17],
            [false, 'Lease End Date', 'date_format(lease_payables.lease_end, "%d/%m/%Y")', 18],
            [false, 'Landlord Name', 'landlord.name', 19],
            [false, 'Managing Agent Name', 'managing_agent.name', 20],
            [false, 'Passing Rent', 'lease_charges.period', 21],
            [false, 'Passing Rent Frequency', 'lease_charges.frequency', 22],
            [false, 'Service Charge', 'lease_charges.annual', 23],
            [false, 'Insurance Charge', 'lease_charges.insurance', 24],
            [false, 'Rent Review Pattern', 'lease_payables.review_pattern', 25],
            [false, 'Next Review Date', 'date_format(lease_payables.next_review, "%d/%m/%Y")', 26],
            [false, 'Rent Review Basis', 'lease_payables.review_basis', 27],
            [false, 'Break Option', 'break_party_options.name', 28],
            [true, 'Break Type', 'if(lease_breaks.type="Date", "Fixed", lease_breaks.type)', 29],
            [false, 'Break Date', 'lease_breaks.date', 30],
            [false, 'Break Min Notice Periods (months)', '""', 31],
            [false, 'Break Penalty', 'lease_breaks.penalty', 32],
            [false, 'Break Penalty/Incentive', 'lease_breaks.penalty_incentive', 33],
            [false, 'Break Penalty Notes', 'lease_breaks.notes', 34],
            [false, 'Break Notice Served?', '""', 35],
            [false, 'Break Effective From', '""', 36],
            [false, 'L&T 1954 Exclusion', 'lease_payables.outside_54_act', 37],
            [
                false,
                'Cluttons Property Manager Name',
                "CONCAT_WS(
                    ' ',
                    property_manager_user.first_name,
                    property_manager_user.last_name
                )",
                38
            ],
            [false, 'Cluttons Property Manager Email', 'property_manager_user.email', 39],
            [false, 'Cluttons Property Manager Telephone', "'-'", 40],
        ],
        'lease_payable_rent_review' => [
            [false, 'Client Ref', 'client_accounts.yardi_client_ref', 0],
            [true, 'Client Name', 'client_accounts.name', 1],
            [false, 'Portfolio Ref', 'portfolios.yardi_portfolio_ref', 2],
            [false, 'Portfolio Name', 'portfolios.name', 3],
            [false, 'Yardi Property Ref', 'properties.yardi_property_ref', 4],
            [false, 'Client Property Ref', 'properties.yardi_alt_ref', 5],
            [true, 'Property Name', 'properties.name', 6],
            [
                false,
                'Property Address',
                "CONCAT_WS(
                    ' ',
                    addresses.unit,
                    addresses.number,
                    addresses.building,
                    addresses.street,
                    addresses.town,
                    addresses.postcode,
                    counties.name,
                    countries.name
                )",
                7
            ],
            [false, 'Property Tenure', 'property_tenures.name', 8],
            [false, 'Property Main Use', 'property_uses.name', 9],
            [true, 'Yardi Lease Ref', 'lease_payables.cluttons_lease_ref', 10],
            [true, 'Alternate Lease Ref', 'lease_payables.client_lease_ref', 11],
            [false, 'Lease Description', '""', 12],
            [false, 'Term (Years)', '""', 13],
            [false, 'Term (Months)', '""', 14],
            [false, 'Term (Days)', '""', 15],
            [false, 'Date of Agreement', 'date_format(lease_payables.agreement_date, "%d/%m/%Y")', 16],
            [false, 'Lease Start Date', 'date_format(lease_payables.lease_start, "%d/%m/%Y")', 17],
            [false, 'Lease End Date', 'date_format(lease_payables.lease_end, "%d/%m/%Y")', 18],
            [false, 'Landlord Name', 'landlord.name', 19],
            [false, 'Managing Agent Name', 'managing_agent.name', 20],
            [false, 'Passing Rent', 'lease_charges.period', 21],
            [false, 'Passing Rent Frequency', 'lease_charges.frequency', 22],
            [false, 'Service Charge', 'lease_charges.annual', 23],
            [false, 'Insurance Charge', 'lease_charges.insurance', 24],
            [false, 'Rent Review Pattern', 'lease_payables.review_pattern', 25],
            [false, 'Next Review Date', 'date_format(lease_payables.next_review, "%d/%m/%Y")', 26],
            [false, 'Rent Review Basis', 'lease_payables.review_basis', 27],
            [false, 'Break Option', 'break_party_options.name', 28],
            [false, 'Next Break Date', 'lease_payables.next_break_date', 30],
            [false, 'Break Min Notice Periods (months)', '""', 31],
            [false, 'Break Penalty', 'lease_breaks.penalty', 32],
            [false, 'Break Penalty/Incentive', 'lease_breaks.penalty_incentive', 33],
            [false, 'Break Penalty Notes', 'lease_breaks.notes', 34],
            [false, 'Break Notice Served?', '""', 35],
            [false, 'Break Effective From', '""', 36],
            [false, 'L&T 1954 Exclusion', 'lease_payables.outside_54_act', 37],
            [
                false,
                'Cluttons Property Manager Name',
                "CONCAT_WS(
                    ' ',
                    property_manager_user.first_name,
                    property_manager_user.last_name
                )",
                38
            ],
            [false, 'Cluttons Property Manager Email', 'property_manager_user.email', 39],
            [false, 'Cluttons Property Manager Telephone', "''", 40],
        ],
        'lease_payable_expiry_dates' => [
            [false, 'Client Ref', 'client_accounts.yardi_client_ref', 0],
            [true, 'Client Name', 'client_accounts.name', 1],
            [false, 'Portfolio Ref', 'portfolios.yardi_portfolio_ref', 2],
            [false, 'Portfolio Name', 'portfolios.name', 3],
            [false, 'Yardi Property Ref', 'properties.yardi_property_ref', 4],
            [false, 'Client Property Ref', 'properties.yardi_alt_ref', 5],
            [true,  'Property Name', 'properties.name', 6],
            [
                false,
                'Property Address',
                "CONCAT_WS(
                    ' ',
                    addresses.unit,
                    addresses.number,
                    addresses.building,
                    addresses.street,
                    addresses.town,
                    addresses.postcode,
                    counties.name,
                    countries.name
                )",
                7
            ],
            [false, 'Property Tenure', 'property_tenures.name', 8],
            [false, 'Property Main Use', 'property_uses.name', 9],
            [true,  'Yardi Lease Ref', 'lease_payables.cluttons_lease_ref', 10],
            [true,  'Alternate Lease Ref', 'lease_payables.client_lease_ref', 11],
            [false, 'Lease Description', '""', 12],
            [false, 'Term (Years)', '""', 13],
            [false, 'Term (Months)', '""', 14],
            [false, 'Term (Days)', '""', 15],
            [false, 'Date of Agreement', 'date_format(lease_payables.agreement_date, "%d/%m/%Y")', 16],
            [false, 'Lease Start Date', 'date_format(lease_payables.lease_start, "%d/%m/%Y")', 17],
            [false, 'Lease End Date', 'date_format(lease_payables.lease_end, "%d/%m/%Y")', 18],
            [false, 'Landlord Name', 'landlord.name', 19],
            [false, 'Managing Agent Name', 'managing_agent.name', 20],
            [false, 'Passing Rent', 'lease_charges.period', 21],
            [false, 'Passing Rent Frequency', 'lease_charges.frequency', 22],
            [false, 'Service Charge', 'lease_charges.annual', 23],
            [false, 'Insurance Charge', 'lease_charges.insurance', 24],
            [false, 'Rent Review Pattern', 'lease_payables.review_pattern', 25],
            [false, 'Next Review Date', 'date_format(lease_payables.next_review, "%d/%m/%Y")', 26],
            [false, 'Rent Review Basis', 'lease_payables.review_basis', 27],
            [false, 'Break Option', '""', 28],
            [true,  'Expiry Date', 'date_format(lease_payables.lease_end, "%d/%m/%Y")', 29],
            [false, 'Next Break Date', 'lease_breaks.date', 30],
            [false, 'Break Min Notice Periods (months)', 'lease_breaks.min_notice', 31],
            [false, 'Break Penalty', 'lease_breaks.penalty', 32],
            [false, 'Break Penalty/Incentive', 'lease_breaks.penalty_incentive', 33],
            [false, 'Break Penalty Notes', 'lease_breaks.notes', 34],
            [false, 'Break Notice Served?', '""', 35],
            [false, 'Break Effective From', '""', 36],
            [false, 'L&T 1954 Exclusion', 'lease_payables.outside_54_act', 37],
            [
                false,
                'Cluttons Property Manager Name',
                "CONCAT_WS(
                    ' ',
                    property_manager_user.first_name,
                    property_manager_user.last_name
                )",
                38
            ],
            [false, 'Cluttons Property Manager Email', 'property_manager_user.email', 39],
            [false, 'Cluttons Property Manager Telephone', "''", 40],
        ]

    ];
}
