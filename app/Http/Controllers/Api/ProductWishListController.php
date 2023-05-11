<?php

namespace App\Http\Controllers\Api;

use App\Actions\ProductWishList\AddWishProduct;
use App\Actions\ProductWishList\DeleteWishListItem;
use App\Actions\ProductWishList\GetProductWishList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductWishListController extends Controller
{
    public function create(Request $request){
        return (new AddWishProduct($request))->execute();
    }

    public function index(Request $request){
        return (new GetProductWishList($request))->execute();
    }

    public function delete(Request $request){
        return (new DeleteWishListItem($request))->execute();
    }
}
