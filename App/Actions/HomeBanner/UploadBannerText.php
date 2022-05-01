<?php

namespace App\Actions\HomeBanner;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\HomeBanner;

class UploadBannerText extends Action
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'id' => 'required|integer|exists:home_banners,id',
            'banner_link' => 'required|url|max:255'
        ]);
        return $this->valResult($val);
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            HomeBanner::where('id',$this->request->id)->update([
                'banner_link' => $this->request->banner_link
            ]);
            return $this->successMessage('Banner link updated successfully');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
