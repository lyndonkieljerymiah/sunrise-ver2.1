<?php

namespace App\Http\Reports;

use App\Http\Reports\BaseReport;
use App\Http\Datasource\Villas\VillaMasterlist;



class VillaMasterListReport extends BaseReport {


    public function __construct($params) {
        
        $this->dataSource = new VillaMasterlist($params);
    }

    public function getTemplateSource()
    {
        return "villa-master-list";
    }

    public function isPdfRender()
    {
        return false;
    }

    public function isApi() {
        return true;
    }

    public function getLookups()
    {
        // TODO: Implement getLookups() method.
        $lookups = Selection::getSelections(["villa_location"]);
        return $lookups;
    }

}