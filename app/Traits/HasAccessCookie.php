<?php
namespace App\Traits;

use App\Models\Cookie;
use Carbon\Carbon;

trait HasAccessCookie{

    protected function generateCookie(){
        while(true){
           $val = mt_rand(10000000,90000000000000);
           if(!Cookie::where('cookie_value',$val)->exists()){
             return $val;
           }
        }
     }
     
     protected function saveCookie(){
       $data = Cookie::create([
          'cookie_name'=>'basic_access',
          'cookie_value'=>$this->generateCookie(),
          'expiry'=> now()->addMonths(12)
       ]);
       return $data;
     }
     
     protected function getCookiePayload($cookie){
        $t = cookie(
           'basic_access',
           $cookie->cookie_value,
           12 * 31 * 24 * 60, null,null,null,true,false,null
        );
        return $t;
     }

    protected function isEligibleForNewCookie(){
        if($this->request->bearerToken() == null && $this->request->hasCookie('_token') == false){
           $record = Cookie::where('cookie_value',$this->request->cookie('basic_access'))
           ->where('cookie_name','basic_access')
           ->where('status',1)->first();
           if(isset($record)){
              $expiry =new Carbon($record->expiry);
              if(now()->greaterThan($expiry)){
                 $record->update(['status' => 0]);
                 return true;
              }else{
                 return false;
              }
           }else{
              return true;
           }
        }else{
           return false;
        }
     }

}
    