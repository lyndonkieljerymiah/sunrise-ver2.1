<?php


namespace App\Http\Datasource\Villas;


use App\Villa;
use Carbon\Carbon;
use App\Selection;
use App\Traits\ArrayGroupTrait;
use App\Http\Datasource\IDataSource;

class VillaMasterlist implements IDataSource {

    use ArrayGroupTrait; 


    private $params;

    public function __construct($params) {
        $this->params = $params;
    }
    public function execute() {


        $location = $this->params["location"];
        $villa = Villa::with(["contracts" => function($query) {
            $query->orderBy("period_start","desc");
        }])
        ->where('location', $location)
        ->orderBy('villa_no')
        ->get();

        $rows = $this->arrayGroupBy($villa,function($row) {
            $item = [
                "id"                =>  $row->getId(),
                "villa_no"          =>  $row->villa_no,
                "location"          =>  $row->location,
                "description"       =>  $row->description,
                "electricity_no"    =>  $row->electricity_no,
                "water_no"              =>  $row->water_no,
                "qtel_no"               =>  $row->qtel_no,
                "villa_class"           =>  $row->villa_class,
                "current_rate"          =>  $row->rate_per_month,
                "status"                =>  $row->status,
                "period_end"            =>  $row->contracts()->first()->period_end,
                "days_vacant"           =>  Carbon::now()->diffInDays(Carbon::parse($row->contracts()->first()->period_end))
            ];

            return $item;

        },["location"]);


        return [
            'data'      => $rows,
            'params'    =>  [
                'location'  => Selection::getValue("villa_location",$location),
                'as_of'     =>  Carbon::now()->format('d M Y')
            ]
        ];
    }
    
}
