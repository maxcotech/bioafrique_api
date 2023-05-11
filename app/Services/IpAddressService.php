<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpAddressService{
    public function __construct(){
       $this->base_url = env("IP_GEOLOCATION_BASE_URL");
       $this->api_key = env("IP_GEOLOCATION_API");
    }

    public function getIpGateWayUrl($ip_address){
        return $this->base_url."json/".$this->api_key."/".$ip_address;
    }

    public function getLocationByIp($ip_address){
        try{
            $url = $this->getIpGateWayUrl($ip_address);
            $resp = Http::get($url);
            $data = null;
            if($resp->ok()){
                $data = json_decode($resp->body());
                $data->url = $url;
            }
            return $data;
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return (object) [
                'country_code' => "USA",
                'country_name' => "United States",
                'extra' => "Fallback Value"
            ];
        }
    }



}
    