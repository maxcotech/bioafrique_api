<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Traits\HasHttpResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    use HasHttpResponse;

    public function index(Request $request){
        $paginate = $request->query('paginate',0);
        if($paginate == 0){
            return $this->successWithData(Country::all());
        } else {
            return $this->successWithData(Country::paginate(15));
        }
    }

    public function getCurrencies(Request $request){
        $paginate = $request->query('paginate',0);
        if($paginate == 0){
            return $this->successWithData(Currency::all());
        } else {
            return $this->successWithData(Currency::paginate(15));
        }
    }
}
