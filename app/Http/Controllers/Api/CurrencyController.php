<?php

namespace App\Http\Controllers\Api;

use App\Actions\Currency\CreateCurrency;
use App\Actions\Currency\DeleteCurrency;
use App\Actions\Currency\UpdateCurrency;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Traits\HasHttpResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    use HasHttpResponse;
    public function index(Request $request){
        $paginate = $request->query('paginate',0);
        $country_id = $request->query('country_id',null);
        $currency_query = new Currency();
        if(isset($country_id)){
            $currency_query = $currency_query->where('country_id',$country_id);
        }
        if($paginate == 0){
            return $this->successWithData($currency_query->get());
        } else {
            return $this->successWithData($currency_query->paginate(15));
        }
    }

    public function create(Request $request){
        return (new CreateCurrency($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateCurrency($request))->execute();
    }
    public function delete(Request $request,$currency_id){
        return (new DeleteCurrency($request,$currency_id))->execute();
    }
}
