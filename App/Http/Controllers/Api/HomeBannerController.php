<?php

namespace App\Http\Controllers\Api;

use App\Actions\HomeBanner\UploadBanner;
use App\Actions\HomeBanner\UploadBannerText;
use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use App\Traits\FilePath;
use App\Traits\HasFile;
use App\Traits\HasHttpResponse;
use Illuminate\Http\Request;

class HomeBannerController extends Controller
{
    use HasHttpResponse,HasFile,FilePath;

    public function create(Request $request){
        return (new UploadBanner($request))->execute();
    }
    public function index(){
        $data = HomeBanner::all();
        return $this->successWithData($data);
    }

    public function delete($banner_id){
        try{
            $banner = HomeBanner::find($banner_id);
            $initial_path = $this->getInitialPath($banner->banner,UploadBanner::uploadPath);
            $this->deleteFile($initial_path);
            $banner->delete();
            return $this->successMessage('Home Banner deleted successfully');
        }
        catch(\Exception $e){
            return $this->internalError($e->getMessage());
        }
       
    }

    public function uploadText(Request $request){
        return (new UploadBannerText($request))->execute();
    }
}
