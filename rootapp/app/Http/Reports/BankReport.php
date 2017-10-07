<?php

namespace App\Http\Reports;


use App\Http\Datasource\Bank\BankDepositDetail;
use App\Http\Datasource\Bank\BankDepositSummary;

class BankReport extends BaseReport
{

    private $templateSource;


    public function __construct($params)
    {
        if(isset($params['report_type']) && $params['report_type'] == "summary") {
            $this->dataSource = new BankDepositSummary($params);
            $this->templateSource = "bank-account-summary";
        }
        else {
            $this->dataSource = new BankDepositDetail($params);
            $this->templateSource = "bank-account-detail";
        }
        
    }

    public function getTemplateSource()
    {
        return $this->dataSource;
    }

    public function isPdfRender()
    {
        return false;
    }


    public function isApi()
    {
        return false;
    }

    public function getLookups()
    {
        // TODO: Implement getLookups() method.
    }
}