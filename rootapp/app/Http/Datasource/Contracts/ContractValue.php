<?php

namespace App\Http\Datasource\Contracts;

use App\Contract;
use App\Http\Datasource\IDataSource;
use App\Selection;
use App\Traits\ArrayGroupTrait;
use App\Traits\HelperTrait;
use App\Traits\QuerySoftDeleteTrait;
use Carbon\Carbon;


class ContractValue implements IDataSource
{
    use ArrayGroupTrait;

    use QuerySoftDeleteTrait;

    use HelperTrait;

    private $params;


    public function __construct($params)
    {
        $this->params = $params;
    }

    public function execute()
    {

        $contract_status = isset($this->params['contract_status']) ? $this->params['contract_status'] : 'pending';
        $contract_year = isset($this->params['contract_year']) ? $this->params['contract_year'] : Carbon::now()->year;
        $location = isset($this->params['location']) ? $this->params['location'] : 'sv1';

        $recordset = $this->createDb('contracts')
                        ->join('villas','villas.id','=','contracts.villa_id')
                        ->join('tenants','tenants.id','=','contracts.tenant_id')
                        ->where('contracts.status',$contract_status)
                        ->where('villas.location',$location)
                        ->orderBy('villas.villa_no')
                        ->select('villas.villa_no','contracts.contract_no',
                            'contracts.created_at','tenants.full_name',
                            'contracts.period_start','contracts.period_end',
                            'villas.rate_per_month','contracts.amount','contracts.status')->get();

        $rows = $this->arrayGroup($recordset,function($row) {
            $item = [
                'villa_no'      =>  $row->villa_no,
                'contract_no'   =>  $row->contract_no,
                'date_entry'    =>  \Carbon\Carbon::parse($row->created_at)->format('d M y'),
                'full_name'     =>  $row->full_name,
                'date_period'   =>  \Carbon\Carbon::parse($row->period_start)->format('d M y')." - ".\Carbon\Carbon::parse($row->period_end)->format('d M y'),
                'total_year'    =>  $this->calculateTotalYearMonth($row->period_start,$row->period_end),
                'rate_per_month'    =>  number_format($row->rate_per_month,2),
                'full_value'        =>  number_format($row->amount,2),
                'contract_status'   =>  ucfirst($row->status)];
            return $item;
        });

        $rows["total"] = $recordset->sum('amount');
        $rows["year"] = $contract_year;
        $rows["location"] = Selection::getValue('villa_location',$location);


        return $rows;


    }
}