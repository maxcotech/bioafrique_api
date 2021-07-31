<?php
namespace App\Traits;

trait HasUserType{
    protected function studentType(){
        return 1;
    }
    protected function adminType(){
        return 2;
    }

}
