<?php

namespace App\Actions\HomeBanner;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\HomeBanner;
use App\Traits\FilePath;
use App\Traits\HasFile;

class UploadBanner extends Action
{
    use HasFile,FilePath;
    public const uploadPath = "home_banners";
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'banner' => 'required|file|mimes:jpg,png,gif,webp,jpeg',
            'id' => 'nullable|integer|exists:home_banners,id'
        ]);
        return $this->valResult($val);
    }
    

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $file_url = $this->uploadImage($this->request->banner,self::uploadPath);
            $id = $this->request->id;
            $new_id = null;
            if(isset($id)){
                $old_banner = HomeBanner::find($id);
                $initial_path = $this->getInitialPath($old_banner->banner,self::uploadPath);
                $this->deleteFile($initial_path);
                $old_banner->update(['banner' => $file_url]);
                $new_id = $id;
            } else {
                $new_banner = HomeBanner::create(['banner'=> $file_url]);
                $new_id = $new_banner->id;
            }
            return $this->successWithData([
                'id' => $new_id,
                'banner' => $this->getRealPath($file_url)
            ],'Banner Image uploaded successfully');
        
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
