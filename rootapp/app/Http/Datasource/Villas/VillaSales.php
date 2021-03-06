<?php


namespace App\Http\Datasource\Villas;


use App\Http\Datasource\IDataSource;
use Carbon\Carbon;

use App\Traits\ArrayGroupTrait;
use App\Traits\QuerySoftDeleteTrait;

class VillaSales implements IDataSource
{

    use QuerySoftDeleteTrait;
    
    use ArrayGroupTrait;

    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function execute()
    {
        $month_from = isset($this->params['month_from']) ? (int)$this->params['month_from'] : '';
        $month_to = isset($this->params['month_to']) ? (int)$this->params['month_to'] : '';
        $location = isset($this->params['location']) ? $this->params['location'] : 'sv1';
        $year = isset($this->params['year']) ? (int)$this->params['year'] : \Carbon\Carbon::now()->year;



        //create two queries
        $recordset = $this->createDb('villas')
            ->join('contracts', 'contracts.villa_id', '=', 'villas.id')
            ->join('contract_bills', 'contract_bills.contract_id', '=', 'contracts.id')
            ->join('payments', 'payments.bill_id', '=', 'contract_bills.id')
        ->select("villas.villa_no",
                "payments.status AS payment_status",
                \DB::raw("YEAR(payments.period_start) AS year_schedule"),
                \DB::raw("MONTH(payments.period_start) AS monthly_schedule"),
                \DB::raw("SUM(payments.amount) AS monthly_payable"),
                \DB::raw("(SELECT SUM(amount) FROM payments where status ='clear' AND bill_id = contract_bills.id) AS total_payable"))
        ->groupBy(
            "villas.villa_no",
            \DB::raw("MONTH(payments.period_start)"))
        ->whereNull("contracts.deleted_at")
        ->where("villas.location",$location)
        ->where(\DB::raw("YEAR(payments.period_start)"),$year)
        ->orderBy("villas.villa_no")
        ->get();

        $groupParent = [];
        $groupSummary = $this->arrayGroupBy($recordset,null,["villa_no","monthly_schedule"]);
        for($i = $month_from;$i <= $month_to;$i++) {
           $headers[] = date('M', mktime(0, 0, 0, $i, 10));
        }

        $ret_value = [
            'location'      =>  \App\Selection::getValue("villa_location",$location),
            'year'          =>  $year,
            'month_from'    => $month_from,
            'month_to'      => $month_to,
            'data'          => $groupSummary
        ];

        return $ret_value;

        // // $recordset = $this->createDb('villas')
        // //     ->join('contracts', 'contracts.villa_id', '=', 'villas.id')
        // //     ->join('contract_bills', 'contract_bills.contract_id', '=', 'contracts.id')
        // //     ->join('payments', 'payments.bill_id', '=', 'contract_bills.id')
        // //     ->groupBy("villas.villa_no","villas.rate_per_month",
        // //             \DB::raw("MONTH(payments.period_start)"))
        // //     ->select(
        // //         \DB::raw("villas.villa_no,
        // //                 villas.rate_per_month,
        // //                 villas.status,
        // //                 contract_bills.id AS contract_bill_id,
        // //                 MONTH(payments.period_start) total_month,
        // //                 SUM(payments.amount) AS total_payments"))
        // //     ->where('villas.location', $location)
        // //     ->where(\DB::raw('YEAR(payments.period_start)'),$year)
        // //     ->where('payments.status','clear')
        // //     ->whereBetween(\DB::raw('MONTH(payments.period_start)'), [$month_from, $month_to])
        // //     ->orderBy('villas.villa_no')
        // //     ->get();

        // $column_month = [];
        // $rows = $this->arrayGroup($recordset,function($row) use(&$column_month) {
        //     $item = [
        //         'villa_no' => $row->villa_no,
        //         'rate_per_month' => $row->rate_per_month,
        //         'number_month' => $row->total_month,
        //         'total_payments' => $row->total_payments,
        //         'villa_status' => $row->status
        //     ];

        //     if(isset($column_month[$row->total_month])) {
        //         $p = &$column_month[$row->total_month];
        //         $p['total'] = floatval($p['total']) + floatval($row->total_payments);
        //     }
        //     else {
        //         $column_month[$row->total_month] = [
        //             'date_name' => date('M', mktime(0, 0, 0, $row->total_month, 10)),
        //             'total'     =>  floatval($row->total_payments)];
        //     }

        //     return $item;
        // });

        // ksort($column_month);
        // $rows['months'] = $column_month;
        // $rows['location'] = \App\Selection::getValue("villa_location",$location);
        // $rows['period'] = [
        //     'from'  =>  date('F', mktime(0, 0, 0, $month_from, 10)),
        //     'to'    =>  date('F', mktime(0, 0, 0, $month_to, 10)),
        //     'year'  =>  $year
        // ];

        // // TODO: Implement execute() method.

        // return $rows;
    }
}