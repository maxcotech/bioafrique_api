<?php

namespace App\Http\Controllers\Api;

use App\Actions\Brands\CreateBrand;
use App\Actions\Brands\DeleteBrand;
use App\Actions\Brands\GetBrands;
use App\Actions\Brands\UpdateBrand;
use App\Actions\Brands\UpdateBrandStatus;
use App\Actions\Brands\UploadBrandImage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function create(Request $request){
        return (new CreateBrand($request))->execute();
    }

    public function uploadLogo(Request $request){
        return (new UploadBrandImage($request))->execute();
    }

    public function index(Request $request){
        return (new GetBrands($request))->execute();
    }

    public function update(Request $request){
        return (new UpdateBrand($request))->execute();
    }

    public function delete(Request $request,$brand_id){
        return (new DeleteBrand($request,$brand_id))->execute();
    }
    public function updateBrandStatus(Request $request){
        return (new UpdateBrandStatus($request))->execute();
    }
}
