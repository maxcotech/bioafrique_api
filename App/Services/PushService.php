<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushServices{
    protected $app_id,$base_url;
    protected $headers,$subtitle,$contents,$url;
    protected $headings,$extra_data,$route;
    public function __construct()
    {
        $this->app_id=env('ONESIGNAL_APP_ID');
        $this->base_url=env('APP_BASE_URL');

    }
    protected function isAppIdSet(){
        if($this->app_id == null){
            return false;
        }
        else{
            return true;
        }
    }
    protected function isBaseUrlSet(){
        if($this->base_url == null){
            return false;
        }else{
            return true;
        }
    }
    public function setHeadings($new_heading){
        if(is_array($new_heading)){
            $this->headings=$new_heading;
        }else{
            $this->headings=['en'=>$new_heading];
        }
        return $this;
    }
    public function setSubTitle($new_subtitle){
        if(is_array($new_subtitle)){
            $this->subtitle=$new_subtitle;
        }else{
            $this->subtitle=['en'=>$new_subtitle];
        }
        return $this;
    }
    public function setContents($contents){
        if(is_array($contents)){
            $this->contents=$contents;
        }else{
            $this->contents=['en'=>$contents];
        }
        return $this;
    }
    public function setExtraData($data){
        $this->extra_data=$data;
        return $this;
    }
    public function setRoute($route){
        $this->route=$route;
        return $this;
    }
    protected function compilePushPayload($receivers,$receiver_type){
        $payload=[];
        $payload['app_id']=$this->app_id;
        if($this->extra_data !== null) $payload['data']=$this->extra_data;
        if($this->contents != null) $payload['contents']=$this->contents;
        $payload[$receiver_type]=$receivers;
        if($this->headings != null) $payload['headings']=$this->headings;
        if($this->subtitle != null) $payload['subtitle']=$this->subtitle;
        if($this->route != null) $payload['url']=$this->route;
        return $payload;

    }
    public function sendPushNotification($player_ids,$receiver_type='include_player_ids'){
        if(!$this->isAppIdSet()) return false;
        $payload=$this->compilePushPayload($player_ids,$receiver_type);
        $response=Http::withHeaders(['Content-Type'=>'application/json; charset=utf-8'])
        ->post(env('ONESIGNAL_ENDPOINT'),$payload);
        if($response->failed()){
            if($response->serverError()){
                Log::error('A server error occurred while trying to send push notification for '.$this->base_url);
            }else{
                Log::error('A client error occurred while trying to send push notification for '.$this->base_url);
            }
        }
        return $this;
    }
}
