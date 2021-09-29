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
        $user_ip = $this->getUserIpAdress();
        $ip_service = new IpAddressService();
        $location = $ip_service->getLocationByIp($user_ip);
        return $location;
    }
}
