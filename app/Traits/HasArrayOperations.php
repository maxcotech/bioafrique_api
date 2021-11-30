<?php
namespace App\Traits;

trait HasArrayOperations{
    public function extractUniqueValueList($list,$in_key){
        if(!is_object($list) && !is_array($list)) return [];
        $out_list = [];
        $in_list = json_decode(json_encode($list),true);
        foreach($in_list as $item){
            foreach($item as $key => $value)
                if($key === $in_key){
                    if(!in_array($value,$out_list)){
                        array_push($out_list,$value);
                    }
                }
            
        }
        return $out_list;
    }

    public function selectArrayItemByKeyPair($in_key,$in_value,$in_list){
        if(!is_object($in_list) && !is_array($in_list)) return null;
        $in_list = json_decode(json_encode($in_list),true);
        foreach($in_list as $list_item){
            foreach($list_item as $key => $value){
                if($in_key == $key && $in_value == $value){
                    return $list_item;
                }
            }
        }
        return null;
    }

    public function sumArrayValuesByKey($arr,$key){
        if(!is_object($arr) && !is_array($arr)) return 0;
        $in_arr = (array) $arr;
        $total = 0;
        foreach($in_arr as $item){
            $total += $item[$key];
        }
        return $total;
    }

}
    