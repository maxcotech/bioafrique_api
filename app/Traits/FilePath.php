<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

trait FilePath{
    protected function getThumbnailPath(){
        return "thumbnails";
    }
    protected function getRealPath($value){
        if(!isset($value)) return "";
        if(env('CURRENT_DISK') == 's3'){
            return Storage::disk('s3')->url($value);
        }
        return url('uploads')."/".$value;
    }
    protected function createSchoolPath($school_id,$suffix){
        return "schools/school".$school_id."/".$suffix;
    }
    protected function getExtensionFromMime($mime){
        if(!isset($mime)) return $mime;
        $segs = explode("/",$mime);
        if(empty($segs)){
           return $mime;
        }else{
           return $segs[1];
        }
    }
    protected function generateThumbnailPath($extension = 'png',$path = null){
        $name = now()->timestamp.mt_rand(20000000,900000000000);
        $name .= ".".$extension;
        if(isset($path)){
           return $path."/".$name;
        }else{
           return $name;
        }
     }
    protected function getInitialPath($value,$prefix='assets'){
        $segments=explode('/',$value);
        $first_index=$this->getIndexOf($prefix,$segments);
        $new_array=[];$count=0;
        while(true){
            $index=$first_index + $count;
            if($index == count($segments)){
                break;
            }
            array_push($new_array,$segments[$index]);
            $count++;
        }
        $new_url=implode("/",$new_array);
        return $new_url;
    }
    protected function getIndexOf($value,$array){
        foreach($array as $key => $a_value){
            if($a_value == $value){
                return $key;
            }
        }
    }
    protected function thumbnailDiv(){
        return 4;
    }

    protected function createAndUploadThumbnail(UploadedFile $file){
        $img = new ImageManager();
        $img_obj = $img->make($file);
        $thumb_height = $img_obj->height() / $this->thumbnailDiv();
        $thumb_width = $img_obj->width() / $this->thumbnailDiv();
        $img_obj->resize($thumb_width,$thumb_height);
        $img_ext = $file->getClientOriginalExtension();
        $img_data = $img_obj->stream($img_ext);
        $file_path = $this->generateThumbnailPath($img_ext,$this->getThumbnailPath());
        Storage::disk(env('CURRENT_DISK'))->put($file_path, $img_data);
        return $file_path;
     }

}
