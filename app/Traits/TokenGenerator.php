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

}
    