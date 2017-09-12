<?php
/**
 * Created by PhpStorm.
 * User: arnold.mercado
 * Date: 8/7/2017
 * Time: 5:12 PM
 */

namespace App\Http\Datasource\Villas;


use App\Http\Datasource\IDataSource;
use Carbon\Carbon;

class VillaSales implements IDataSource
{

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

        $recordset = \DB::table('villas')
            ->join('contracts', 'contracts.villa_id', '=', 'villas.id')
            ->join('contract_bills', 'contract_bills.contract_id', '=', 'contracts.id')
            ->join('payments', 'payments.bill_id', '=', 'contract_bills.id')
            ->groupBy(\DB::raw("MONTH(payments.effectivity_date),villas.villa_no"))
            ->select(
                \DB::raw("villas.villa_no,
                        villas.description,
                        villas.rate_per_month,
                        villas.location,
                        COUNT(payments.id) total_cheque,
                        villas.electricity_no,
                        villas.water_no, 
                        SUM(payments.amount) total_payments,
                        MONTH(payments.effectivity_date) total_month,
                        YEAR(payments.effectivity_date) total_year"))
            ->where('villas.location', $location)
            ->where('payments.status', '=', 'clear')
            ->where(\DB::raw('YEAR(payments.effectivity_date)'),$year)
            ->whereBetween(\DB::raw('MONTH(payments.effectivity_date)'), [$month_from, $month_to])
            ->orderBy('villas.villa_no')
            ->get();
        
        $rows = [
            'data' => [],
            'total' => 0,
            'months' => [],
            'period' => [
                    'from'  =>  date('F', mktime(0, 0, 0, $month_from, 10)),
                    'to'    =>  date('F', mktime(0, 0, 0, $month_to, 10)),
                    'year'  =>  $year
            ],
        ];
        if(sizeof($recordset) == 0) {
            return $rows;
        }

        $total = 0;
        $number_month = 0;

        foreach ($recordset as $record) {
            $row = [
                'villa_no' => $record->villa_no,
                'rate_per_month' => $record->rate_per_month,
                'electricity_no' => $record->electricity_no,
                'water_no' => $record->water_no,
                'description' => $record->description,
                'total_cheque' => $record->total_cheque,
                'number_month' => $record->total_month,
                'total_payments' => $record->total_payments,
                'year' => $record->total_year,
            ];

            if(isset($month[$row['number_month']])) {
                $p = &$month[$row['number_month']];
                $p['total'] = floatval($p['total']) + floatval($row['total_payments']);
            }
            else {
                $month[$row['number_month']] = [
                    'date_name' => date('M', mktime(0, 0, 0, $record->total_month, 10)),
                    'total'     =>  floatval($record->total_payments)];
            }

            $rows['total'] = $rows['total'] + $row['total_payments'];
            array_push($rows['data'], $row);
        }

        ksort($month);
        $rows['months'] = $month;
        $rows['location'] = \App\Selection::getValue("villa_location",$location);

        // TODO: Implement execute() method.

        return $rows;
    }
}