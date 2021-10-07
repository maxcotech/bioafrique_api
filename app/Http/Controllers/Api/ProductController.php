<?php

namespace App\Http\Controllers\Api;

use App\Actions\Product\CreateProduct;
use App\Actions\Product\UploadGalleryImage;
use App\Actions\Product\UploadProductImage;
use App\Actions\Product\UploadProductVariationImage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function uploadGalleryImage(Request $request){
        return (new UploadGalleryImage($request))->execute();
    }

    public function uploadProductImage(Request $request){
        return (new UploadProductImage($request))->execute();
    }

    public function uploadProductVariationImage(Request $request){
        return (new UploadProductVariationImage($request))->execute();
    }

    public function create(Request $request){
        return (new CreateProduct($request))->execute();
    }
}
