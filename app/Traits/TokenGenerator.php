<?php
namespace App\Traits;

trait TokenGenerator{
    protected function createNumberToken($length = 10){
        $token = "";
        $count = 0;
        while($count < $length){
            $token .= mt_rand(0,9);
            $count++;
        }
        return $token;
    }

    protected function generatePassword($length = 8){
        $characters = [
            '1','2','3','4','5','6','7','8','9',
            'a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z',
            'A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z',
            '$','&','#','@'
        ];

        $max = count($characters) - 1;
        $min = 0;
        $output = "";
        while(strlen($output) < $length){
            $random_num = mt_rand($min,$max);
            $output .= $characters[$random_num];
        }
        return $output;
    }

}
    