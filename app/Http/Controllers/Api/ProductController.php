<?php

namespace App\Http\Controllers\Api;

use App\Actions\Product\CreateProduct;
use App\Actions\Product\DeleteProduct;
use App\Actions\Product\GetAProduct;
use App\Actions\Product\GetCategoryProducts;
use App\Actions\Product\GetProducts;
use App\Actions\Product\GetRecentlyViewed;
use App\Actions\Product\GetStoreProducts;
use App\Actions\Product\UpdateProduct;
use App\Actions\Product\UpdateProductStatus;
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

    public function update(Request $request){
        return (new UpdateProduct($request))->execute();
    }

    public function index(Request $request){
        return (new GetProducts($request))->execute();
    }

    public function getStoreProducts(Request $request){
        return (new GetStoreProducts($request))->execute();
    }

    public function getCategoryProducts(Request $request,$category_param){
        return (new GetCategoryProducts($request,$category_param))->execute();
    }

    public function show(Request $request,$slug){
        return (new GetAProduct($request,$slug))->execute();
    }

    public function delete(Request $request,$product_id){
        return (new DeleteProduct($request,$product_id))->execute();
    }

    public function updateProductStatus(Request $request){
        return (new UpdateProductStatus($request))->execute();
    }

    public function getRecentlyViewed(Request $request){
        return (new GetRecentlyViewed($request))->execute();
    }
}
