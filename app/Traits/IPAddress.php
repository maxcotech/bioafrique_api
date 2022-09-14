<?php

namespace App\Traits;

use App\Services\IpAddressService;

trait IPAddress
{
    public function getUserIpAdress()
    {
        $ip = null;
        //whether ip is from the share internet  
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from the remote address  
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function getUserIpLocation(){
        $req_ip_info = request()->header("X-client-ip-payload");
        $location = null;
        if(isset($req_ip_info)){
            $raw_loc = json_decode($req_ip_info);
            if($raw_loc->countryCode !== null && $raw_loc->countryName !== null){
                $location = json_decode(json_encode([
                    "country_name" => $raw_loc->countryName,
                    "country_code" => $raw_loc->countryCode
                ]));
            }
        }
        if($location == null){
            $user_ip = $this->getUserIpAdress();
            $ip_service = new IpAddressService();
            $location = $ip_service->getLocationByIp($user_ip);
        } 
        return $location;
    }
}
