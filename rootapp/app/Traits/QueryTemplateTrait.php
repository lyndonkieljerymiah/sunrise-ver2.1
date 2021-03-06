<?php
/**
 * Created by PhpStorm.
 * User: arnold.mercado
 * Date: 8/22/2017
 * Time: 4:34 PM
 */

namespace App\Traits;


trait QueryTemplateTrait
{
    public function sqlPaymentDue($bill_id) {
        return \DB::raw("(SELECT SUM(payments.amount) FROM payments 
                            WHERE status='clear' 
                            AND payment_mode='payment' 
                            AND bill_id = ".$bill_id.") AS gross_sale");
    }
}