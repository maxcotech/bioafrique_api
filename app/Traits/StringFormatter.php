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

}
    