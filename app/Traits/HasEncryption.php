<?php
namespace App\Traits;

trait HasEncryption{

    protected $algo = "aes-128-cbc-hmac-sha256";
    protected $default_iv = "$#GinPinTa2104ty";

    function encryptData($data,$passphrase,$iv = null){
        return openssl_encrypt($data,$this->algo,$passphrase,0,$iv ?? $this->default_iv);
    }
    
    function decryptData($data,$passphrase,$iv = null){
        return openssl_decrypt($data,$this->algo,$passphrase,0,$iv ?? $this->default_iv);
    }
    

}
    