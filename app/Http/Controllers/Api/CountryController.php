<?php

namespace App\Http\Controllers\Api;

use App\Actions\Country\CreateCountry;
use App\Actions\Country\DeleteCountry;
use App\Actions\Country\UpdateCountry;
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

    public function create(Request $request){
        return (new CreateCountry($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateCountry($request))->execute();
    }
    public function delete(Request $request,$country_id){
        return (new DeleteCountry($request,$country_id))->execute();
    }
}
