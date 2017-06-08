<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Selection extends BaseModel
{
     //
    public static function getSelections(Array $categories = array()) {

        $categories = static::wherein('category',$categories)->get();
        $lookups = array();
        
        foreach($categories as $key => $value) {
            $lookups[$value['category']][] = $value;    
        }
        return $lookups;
    }
    
    public static function getValue($category,$key) {

        $values = Selection::where('category',$category)->where('code',$key)->orderBy('category')->get();
        
        $retValue = ""; 
        
        foreach($values as $value) {

            $retValue = $value->name;

        }

        return $retValue;
    }

    public static function getValues(Array $keys = array()) {

        $values = Selection::wherein('code',$keys)->get();
        return $values;
    }

    public static function convertCode($code) {

        $code = str_replace("_"," ",$code);
        return ucwords($code);

    }
}
