<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Services\Result;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;


class ContractBill extends BaseModel
{
    
    const DEFAULT_PERIOD = 1;

    protected $appends = ["settled_amount","balance"];
    

    public function __construct(array $attributes = [])
    {
        $this->created_at = Carbon::now();
        $this->updated_at = Carbon::now();

        parent::__construct($attributes);

    }

    public static function createInstance($contractId) {

        $bill = new ContractBill();
        $bill->contract_id = $contractId;
        $bill->bill_no = "";
        $bill->instance = Payment::createInstance();
        $bill->instance->initPeriod(self::DEFAULT_PERIOD);
        $bill->payments = [];

        return $bill;
    }

    public static function createInstanceOfPayment() {
        $instance =  Payment::createInstance();
        $instance->initPeriod(self::DEFAULT_PERIOD);
        return $instance;
    }

    /*********************
     * mutation
     ********************/
    public function getSettledAmountAttribute() {

        return $this->Payments()->where("status","clear")->sum("amount");
    }


    public function getBalanceAttribute() {
        $settled_total = $this->settled_amount;
        $contract_amount = $this->contract()->first()->amount;
        return $this->appends['balance'] = ($contract_amount - $settled_total);

    }


    /*********************
     * navigation
     ********************/
    public function Payments() {

        return $this->hasMany(Payment::class,'bill_id','id');

    }
    public function contract() {

        return $this->belongsTo('App\Contract','contract_id');

    }
    
    public function tenant() {

        return $this->contract()->first()->tenant()->first();

    }
    /******************
    * end navigation
     *******************/
    public function activate() {

        $this->status = 'active';

        return $this;
    }

    /********************************
     *  Payments with status
     **********************************/
    public function withClearedPayments() {

        return $this->payments()->where('status','clear');

    }
    public function withPendingPayments() {
        return $this->payments()->where('payment_mode','payment')->where('status','received');
    }
    public function withPaymentStatusOf($status) {
        return $this->payments()->where('status',$status);
    }

    /********************************
     *  Payments total payment amount
     **********************************/
    public function getSummary() {
        return $this->Payments()->where("status","clear")->sum("amount");
    }

    public function withPaymentLine() {
        return $this->with('Payments');
    }

    public function getBillByNo($billNo) {
        return $this->where('bill_no',$billNo)->get();
    }

    public function getExistingContract($contractNo) {

        $raw =  DB::table('contracts AS c')
                ->select('cb.bill_no')
                ->join('contract_bills AS cb','c.id', '=' ,'cb.contract_id')
                ->where('c.contract_no','=',$contractNo)->first();

        return $raw;

    }

    public function isClear() {
        return $this->hasStatusOf('clear');
    }
    public function isCancel() {
        return $this->hasStatusOf('bounce');
    }
    public function isPending() {
        return $this->hasStatusOf('received');
    }
}
