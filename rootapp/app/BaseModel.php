<?php 


namespace App;


use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model {

    use UserTrait;

    protected $guarded = ['id','created_at','updated_at','deleted_at'];

    protected function beforeSave() {return false;}
    protected function afterSave() {return false;}

    protected function setField($field,$value) {
        
        if(!in_array($field,$this->appends)) {
            if(is_numeric($value)) {
                eval('$this->'. $field . "=" . $value . ";");
            }
            else {
                eval('$this->'. $field . "=\"" . $value . "\";");
            }

        }
    }

   
    public function hasStatusOf($status) {
        return $this->status == $status;
    }

    public function toMap($fields = array()) {
        if(sizeof($fields) > 0) {
            foreach ($fields as $key => $value) {
                //do not include custom attribute
                if(!in_array($key,$this->appends) && !in_array($key,$this->guarded)) {
                    $this->setField($key,$value);
                }
            }
        }

        return $this;
    }

   
    //chaining
    public function explicitSearch($fieldKey,$fieldValue) {
        return $this->where($fieldKey,$fieldValue);
    }

    public function customFilter() {

        if(request('filter_field',false)) {
            
            $filter_field = request('filter_field');
            $filter_value = request('filter_value');

            return $this->where($filter_field,'LIKE','%'.$filter_value.'%');
        }

        return $this;
    }

    public function createNewId() {

        $lastRecord = $this->orderBy('id','desc')->first();
        if($lastRecord == null)
            return 1;

        return $lastRecord->id++;
    }

    public function getId() {
        return $this->id;
    }

    public function save(array $options = [])
    {
        $this->beforeSave();
        return parent::save($options); // TODO: Change the autogenerated stub
    }

    public function saveWithUser() {
        $this->setUser();
        $this->save();
    }
}
