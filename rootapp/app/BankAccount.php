<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends BaseModel
{
    protected $table = "bank_accounts";

    public function payments() {
        return $this->hasMany("App\Payment","bank_account","account_no");
    }
    
    //datasource query 
    public function getAccountSummaryPerYear($year) {
        return $this->payments()
            ->groupBy("bank_account",\DB::raw("MONTH(date_deposited)"))
            ->where(\DB::raw("YEAR(date_deposited)"),$year)
            ->where("status","clear")
            ->orderBy(\DB::raw("MONTH(date_deposited) "))
            ->select(\DB::raw("MONTH(date_deposited) as per_month"),\DB::raw("SUM(amount) as total_amount"));
            
    }

    public function getPaymentDetail($month,$year) {
        return $this->payments()
            ->where("status", "clear")
            ->where(\DB::raw("YEAR(date_deposited)"), $year)
            ->where(\DB::raw("MONTH(date_deposited)"), $month)
            ->orderBy("date_deposited");
    }




}
