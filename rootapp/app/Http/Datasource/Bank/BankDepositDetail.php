<?php

namespace App\Http\Datasource\Bank;

use Carbon\Carbon;
use App\BankAccount;
use App\Traits\HelperTrait;
use App\Traits\ArrayGroupTrait;
use App\Http\Datasource\IDataSource;

class BankDepositDetail implements IDataSource
{
    use ArrayGroupTrait,HelperTrait;

    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function execute()
    {

        $account_no = isset($this->params["account_no"]) ? $this->params["account_no"] : "";
        $date_from = isset($this->params["month_from"]) ? Carbon::parse($this->params["month_from"]) : Carbon::now();
        $date_to = isset($this->params["month_to"]) ? Carbon::parse($this->params["month_to"]) : Carbon::now()->addMonth()->subDay();
        
        $accounts = BankAccount::with(["payments" => function ($query) use ($date_from,$date_to) {
            $query
                ->where("status", "clear")
                ->whereBetween("date_deposited", [$date_from,$date_to])
                ->orderBy("date_deposited");
        }]);

        if($account_no != '') {
            $accounts = $accounts->where("account_no",$account_no)->get();
        }
        
        $rows = $this->arrayGroupBy($accounts, function ($row) {
            $item = [
                "account_no"    =>  $row->account_no,
                "bank_name"     =>  $row->bank_name,
                "payments"      =>  []    
            ];

            foreach ($row->payments as $payment) {
                
                $payment_item = [
                    "deposit_date"          =>  Carbon::parse($payment->date_deposited)->format("d M Y"),
                    "tenant_name"           =>  $payment->bill()->first()->tenant()->full_name,
                    "period_start"          =>  Carbon::parse($payment->bill()->first()->contract()->first()->period_start)->format('d M Y'),
                    "period_end"            =>  Carbon::parse($payment->bill()->first()->contract()->first()->period_end)->format('d M Y'),
                    "payment_mode"          =>  $payment->payment_mode,
                    "payment_no"            =>  $payment->payment_no,
                    "payment_type"          =>  $payment->payment_type,
                    "reciept_no"            =>  $payment->reference_no,
                    "amount"                =>  $payment->amount,
                    "status"                =>  $payment->status
                ];
                
                array_push($item["payments"], $payment_item);
            }
            
            return $item;

        }, ["account_no"]);

        //create summation 
        foreach($rows as &$row) {
            $payments = $row[0]["payments"];
            $total = $this->sum($payments,"amount");
            $row[0]["total_payments"] = $total;
        }


        return [
            "title"     =>  "Bank Deposit Detail Report",
            "data"      =>  $rows,
            "params"    =>  [
                "account_no"    =>  $account_no,
                "month"         =>  "",
                "year"          =>  ""
            ]
        ];
    }
}
