<?php
namespace App\Traits;

trait HasEncryption{

    protected $algo = "aes-128-cbc-hmac-sha256";
    protected function getDefaultIv(){
        return env('DEFAULT_IV'); 
    }

    function encryptData($data,$passphrase,$iv = null){
        return openssl_encrypt($data,$this->algo,$passphrase,0,$iv ?? $this->getDefaultIv());
    }
    
    function decryptData($data,$passphrase,$iv = null){
        return openssl_decrypt($data,$this->algo,$passphrase,0,$iv ?? $this->getDefaultIv());
    }
    

}
    