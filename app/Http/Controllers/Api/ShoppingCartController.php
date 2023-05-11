<?php

namespace App\Http\Controllers\Api;

use App\Actions\ShoppingCart\AddShoppingCartItem;
use App\Actions\ShoppingCart\DeleteShoppingCartItem;
use App\Actions\ShoppingCart\GetShoppingCart;
use App\Actions\ShoppingCart\UpdateShoppingCart;
use App\Http\Controllers\Controller;
use App\Models\ShoppingCartItem;
use App\Traits\HasAuthStatus;
use App\Traits\HasHttpResponse;
use App\Traits\HasShoppingCartItem;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{
    use HasShoppingCartItem;
    use HasAuthStatus,HasHttpResponse;
    public function create(Request $request){
        return (new AddShoppingCartItem($request))->execute();
    }

    public function update(Request $request){
        return (new UpdateShoppingCart($request))->execute();
    }

    public function index(Request $request){
        return (new GetShoppingCart($request))->execute();
    }

    public function delete(Request $request,$cart_id){
        return (new DeleteShoppingCartItem($request,$cart_id))->execute();
    }

    public function getCartCount(Request $request){
        try{
            $auth_type = $this->getUserAuthTypeObject($request->user());
            if(!isset($auth_type)){
                throw new \Exception('An Error occurred, please make sure your browser allows cookies on this app.');
            }
            $count = $this->getTotalCartCount($auth_type);
            return $this->successWithData($count);
        }
        catch(\Exception $e){
            return $this->internalError($e->getMessage());
        }
    }
}
