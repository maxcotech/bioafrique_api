<?php

namespace App\Http\Controllers\Api;

use App\Actions\ShoppingCart\AddShoppingCartItem;
use App\Actions\ShoppingCart\GetShoppingCart;
use App\Actions\ShoppingCart\UpdateShoppingCart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{
    public function create(Request $request){
        return (new AddShoppingCartItem($request))->execute();
    }

    public function update(Request $request){
        return (new UpdateShoppingCart($request))->execute();
    }

    public function index(Request $request){
        return (new GetShoppingCart($request))->execute();
    }
}
