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

    public function capitalizeByDelimiter($text,$delimiter = " "){
        if(isset($text)){
            if(str_contains($text,$delimiter)){
                $text_list = explode($delimiter,$text);
                $formatted_list = [];
                foreach($text_list as $text){
                    array_push($formatted_list,ucfirst($text));
                }
                $output = implode(" ",$formatted_list);
                return $output;
            } else {
                return ucfirst($text);
            }
        }
        return null;
    }

    public function obscureTextPart($text) {
        $obscureChar = "*";
        if($text !== null && $text !== null && $text !== ""){
            $textArr = str_split($text);
            $textLength = count($textArr);
            $result = "";
    
            if($textLength === 1){
                return $obscureChar;
            } else if($textLength > 1 && $textLength <= 3){
            $counter = 0;
            foreach($textArr as $item){
                if($counter === 1){
                    $result .= $obscureChar;
                } else {
                    $result .= $item;
                }
                $counter++;
            }
                return $result;
            } else {
                $textLenQuater = floor($textLength / 3);
                $i = 0;
                foreach($textArr as $text_item){
                    if($i >= $textLenQuater && $i < ($textLenQuater * 2)){
                        $result .= $obscureChar;
                    } else {
                        $result .= $text_item;
                    }
                    $i++;
                }
                return $result;
            }
        }
        return $text;
    }

}



    