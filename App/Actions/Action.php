<?php
namespace App\Actions;
use App\Traits\HasHttpResponse;

class Action{
    use HasHttpResponse;

    protected function boolMessage($message = '',$error = false){
        return ['message' => $message,'error' => $error];
    }

}