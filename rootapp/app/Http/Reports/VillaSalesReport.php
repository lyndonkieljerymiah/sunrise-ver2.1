<?php

namespace App\Http\Reports;


use App\Http\Datasource\Villas\PaymentSchedule;
use App\Http\Datasource\Villas\VillaSales;
use App\Selection;

class VillaSalesReport extends BaseReport
{
    private $reportName;

    public function __construct($params)
    {
        $this->dataSource = new VillaSales($params);
    }

    public function getTemplateSource()
    {
        return "villa-sales-analysis";
    }

    public function isPdfRender()
    {
        return false;
    }

    public function getLookups()
    {
        // TODO: Implement getLookups() method.
        $lookups = Selection::getSelections(["villa_location"]);
        return $lookups;
    }

    public function isApi()
    {
        return false;
    }
}