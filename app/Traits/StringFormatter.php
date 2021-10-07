<?php
namespace App\Traits;

trait StringFormatter{
    public function fromSnakeToCamelCase($text){
        $output = "";
        if(str_contains($text,"_")){
            $str_arr = explode($text,"_");
            foreach($str_arr as $str){
                $output = strtoupper($str)." ";
            }
        } else {
            $output = strtoupper($text);
        }
        return trim($output);
    }

    public function generateSlugFromString($str){
        if(!isset($str) || $str == "") return "";
        $str = trim(strtolower($str));
        $pre_slug_value = preg_replace("/\s+/u",'-',$str);
        $pre_slug_value = preg_replace('/[^a-zA-Z0-9]/',"-",$pre_slug_value);
        while(true){
            if(str_contains($pre_slug_value,'--')){
                $pre_slug_value = \str_replace("--","-",$pre_slug_value);
            } else {
                break;
            }
        }
        $slug_value = trim($pre_slug_value,'-');
        return $slug_value;
    }

}



    