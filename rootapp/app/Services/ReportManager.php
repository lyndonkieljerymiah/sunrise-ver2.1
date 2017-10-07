<?php
/**
 * Created by PhpStorm.
 * User: arnold.mercado
 * Date: 8/2/2017
 * Time: 10:18 AM
 */

namespace App\Services;



use Carbon\Carbon;
use App\Http\Reports\BankReport;
use App\Http\Reports\TenantReport;
use App\Http\Reports\ContractReport;
use App\Http\Reports\VillaFormReport;
use App\Http\Reports\VillaSalesReport;
use App\Http\Reports\ExpenseMasterReport;

use App\Http\Reports\VillaMasterListReport;
use App\Http\Datasource\Villas\PaymentSchedule;

class ReportManager
{

    protected static $reports = [];

    public static function get($key,$params)
    {
        self::$reports = [
            'villa_master'          =>  new VillaFormReport($params,'form'),
            'contract_value'        =>  new ContractReport($params,'value'),
            'contract_pending'      =>  new ContractReport($params,'pending'),
            'contract_expiry'       =>  new ContractReport($params,'expiry'),
            'contract_active'       =>  new ContractReport($params,'active'),
            'payment_schedule'      =>  new ContractReport($params,'payment_schedule'),
            'villa_payment'         =>  new VillaSalesReport($params),
            'expense_property'      =>  new ExpenseMasterReport($params),
            'villa_history'         =>  new VillaFormReport($params,'ledger'),
            'tenant_history'        =>  new TenantReport($params),
            'bank_report'           =>  new BankReport($params),
            'villa_master_list'     =>  new VillaMasterListReport($params)
        ];
        
        return self::$reports[$key];
    }

    public static function getReportList()
    {
        $reportList = [
            'contract' => [
                'title' =>  'Contract Report',
                'data' =>  [
                    [
                        'id' => 2,
                        'report_title' => 'Villa Master List - Contract Value',
                        'report_name' => 'contract_value',
                        'created' => Carbon::now()->toDateString()
                    ],
                    [
                        'id' => 7,
                        'report_title' => 'Contract Master File - Active',
                        'report_name' => 'contract_active',
                        'created' => Carbon::now()->toDateString()
                    ],
                    [
                        'id' => 5,
                        'report_title' => 'Contract Master File - Pending',
                        'report_name' => 'contract_pending',
                        'created' => Carbon::now()->toDateString()
                    ],
                    [
                        'id' => 6,
                        'report_title' => 'Contract Master File - Expiring Contract',
                        'report_name' => 'contract_expiry',
                        'created' => Carbon::now()->toDateString()
                    ],
                    [
                        'id' => 8,
                        'report_title' => 'Contact Payment Schedule',
                        'report_name' => 'payment_schedule',
                        'created' => Carbon::now()->toDateString()
                    ],

                ]
            ],
            'villa' => [
                'title' =>  'Villa Report',
                'data'  =>  [
                    [
                        'id' => 1,
                        'report_title' => 'Villa Master List',
                        'report_name' => 'villa_master',
                        'created' => Carbon::now()->toDateString()
                    ],

                    [
                        'id' => 3,
                        'report_title' => 'Villa Master - Payment',
                        'report_name' => 'villa_payment',
                        'created' => Carbon::now()->toDateString()
                    ],

                ]
            ],
            'expenses' => [
                'title' =>  'Expenses Report',
                'data'  =>  [
                    [
                        'id' => 4,
                        'report_title' => 'Expenses Master List - Property',
                        'report_name' => 'expense_property',
                        'created' => Carbon::now()->toDateString(),
                    ]
                ],
            ]
        ];

        return $reportList;
    }

    public static function getParameter() {
        $params = [
            [
                'report_id' => 2,
                'inputs' => [
                    ['label' => 'Status', 'type' => 'dropdown','selection' => 'statuses', 'model' => 'contract_status','default_text' => 'All','default' => ''],
                    ['label' => 'Year', 'type' => 'number', 'model' => 'contract_year', 'placeholder' => 'Enter Year (2017)'],
                    ['label' => 'Location', 'type' => 'dropdown','selection' => 'villa_location','model' => 'location','default_text' => '--Select Location--', 'default' => '' ],
                ],
                'models' => [
                    'contract_status' => '',
                    'contract_year' => '',
                    'location' => ''
                ],
                'lookups' => [],
            ],
            [
                'report_id' => 1,
                'inputs' => [
                    ['label' => 'Enter Villa No', 'type' => 'dropdown','selection' => 'villas','model' => 'villa_no','default_text' => 'All','default' => ''],
                ],
                'models' => [
                    'villa_no' => ''
                ],
                'lookups' => []
            ],
            [
                'report_id' => 3,
                'inputs' =>
                    [
                        ['label' => 'Location', 'type' => 'dropdown','selection' => 'villa_location','model' => 'location','default_text' => '--Select Location--', 'default' => '' ],
                        ['label' => 'Month From', 'type' => 'number','model' => 'month_from','placeholder' => 'Enter Month From'],
                        ['label' => 'Month To', 'type' => 'number', 'model' => 'month_to', 'placeholder' => 'Enter Month To'],
                        ['label' => 'Year', 'type' => 'number', 'model' => 'year', 'placeholder' => 'Enter Month Year'],

                    ],
                'models' => [
                    'month_from' => '',
                    'month_to' => '',
                    'year' => '',
                    'location' => ''
                ],
                'lookups' => []
            ],
            [
                'report_id' => 4,
                'inputs' => [
                        ['label' => 'Location', 'type' => 'dropdown','selection' => 'villa_location','model' => 'location','default_text' => '--Select Location--', 'default' => '' ],
                        ['label' => 'Villa No', 'type' => 'dropdown','selection' => 'villas','model' => 'villa_id','default_text' => 'All','default' => '' ],
                        ['label' => 'Date From', 'type' => 'date','model' => 'date_from'],
                        ['label' => 'Date To', 'type' => 'date','model' => 'date_to'],
                    ],
                'models' => [
                    'location'  =>  '',
                    'villa_id'  =>  '',
                    'date_from' =>  Carbon::now()->toDateString(),
                    'date_to'   =>  Carbon::now()->toDateString()
                ],
                'lookups' => []
            ],
            [
                'report_id' => 6,
                'inputs' => [
                    ['label' => 'Location', 'type' => 'dropdown','selection' => 'villa_location','model' => 'location','default_text' => '--Select Location--', 'default' => '' ],
                    ['label' => 'Date From', 'type' => 'date','model' => 'date_from'],
                    ['label' => 'Date To', 'type' => 'date','model' => 'date_to'],
                ],
                'models' => [
                    'location'  =>  '',
                    'date_from' =>  Carbon::now()->toDateString(),
                    'date_to'   =>  Carbon::now()->toDateString()
                ],
                'lookups' => []
            ],
            [
                'report_id' => 7,
                'inputs' => [
                    ['label' => 'Location', 'type' => 'dropdown','selection' => 'villa_location','model' => 'location','default_text' => '--Select Location--', 'default' => '' ],
                ],
                'models' => [
                    'location'  =>  '',
                ],
                'lookups' => []
            ],
            [
                'report_id' => 8,
                'inputs' =>
                    [
                        ['label' => 'Month From', 'type' => 'number','model' => 'month_from','placeholder' => 'Enter Month From'],
                        ['label' => 'Month To', 'type' => 'number', 'model' => 'month_to', 'placeholder' => 'Enter Month To'],
                        ['label' => 'Year', 'type' => 'number', 'model' => 'year', 'placeholder' => 'Enter Month Year'],
                        ['label' => 'Location', 'type' => 'dropdown','selection' => 'villa_location','model' => 'location','default_text' => '--Select Location--', 'default' => '' ],
                    ],
                'models' => [
                    'month_from' => '',
                    'month_to' => '',
                    'year' => '',
                    'location' => ''
                ],
                'lookups' => []
            ]
        ];

        return [
            'params'    => $params,
        ];
    }
}