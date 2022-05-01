<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasFile{
    protected function uploadImage($file,$path = "files")
    {
        $file_url = null;
        if(isset($file)){
            $file_url = Storage::disk(env('CURRENT_DISK'))->put(
                $path,
                $file
            );
        }
        return $file_url;
    }

    protected function deleteFile($path){
        if(isset($path)){
            return Storage::disk(env('CURRENT_DISK'))->delete($path);
        } else {
            return false;
        }
    }

}
    