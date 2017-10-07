<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Villa extends BaseModel
{
    

    protected $custSelectionKeys = ["villa_class","status","location"];

    protected $table = 'villas';
    
    protected $appends = ['full_rate_per_month','full_location','full_villa_class','full_status'];


    public static function createInstance() {
        $villa =  new Villa();
        $villa->villa_galleries = [];
        return $villa;
    }

    public function __construct(array $attributes = []) {

        $this->location = "sv1";
        $this->villa_no = "";
        $this->electricity_no = "";
        $this->water_no = "";
        $this->qtel_no = "";
        $this->description = "";
        $this->capacity = "0";
        $this->villa_class = "fully_furnished";
        $this->rate_per_month = "0.00";
        parent::__construct($attributes);
    }


    public function VillaGalleries() {

        return $this->hasMany(VillaGallery::class);
    }

    public function contracts() {
        return $this->hasMany(Contract::class,'villa_id');

    }

    public function engageContracts() {
        return $this->hasMany(Contract::class,'villa_id')->whereIn('status',['active','pending']);
    }




    //*******************mutator****************************

    protected function getFullRatePerMonthAttribute() {
        return $this->attributes['full_rate_per_month'] = number_format($this->rate_per_month,2)." ".env("CURRENCY_FORMAT");
    }

    protected function getFullLocationAttribute() {

        return $this->attributes['full_location'] = Selection::getValue('villa_location',$this->location);

    }

    public function getFullVillaClassAttribute() {

        return $this->attributes['full_villa_class'] = Selection::convertCode($this->villa_class);

    }

    public function getFullStatusAttribute() {
        return $this->attributes['full_status'] = Selection::convertCode($this->status);
    }




    /*************************************************************/
    public function isActive() {
        return $this->status == 'active';
    }

    public function vacantOnly() {
        return $this->where('status','vacant');

    }


    public function setToVacant() {
        $this->status = 'vacant';
        return $this;
    }

    public function setToOccupied() {
        $this->status = 'occupied';
        return $this;
    }

    public function statusCount() {
        return $this->select('status',\DB::raw('count(id) as count'))->groupBy('status')->get();
    }
}
