<?php

namespace App\Http\Datasource\Bank;


use App\BankAccount;
use App\Http\Datasource\IDataSource;



class BankDepositSummary implements IDataSource {


    private $params;
    public function __construct($params) {
        $this->params = $params;
    }

    public function execute() {

        $month = isset($this->params['month']) ? $this->params['month'] :  Carbon::now()->month;
        $year =  isset($this->params['year']) ? $this->params['year'] :  Carbon::now()->year;
        $account_no = $this->params['account_no'];
        $accounts = BankAccount::where("account_no",$account_no)->first();
        
        $rows = [
            "account_no"    =>  $accounts->account_no,
            "bank_name"     =>  $accounts->bank_name,
            "payments"      =>  $accounts->getAccountSummaryPerYear($year)->get()
        ];
            
        return [
            "title" =>  "Bank Deposit Summary",
            "data"  =>  $rows,
            "params" => []
        ];
    }


}