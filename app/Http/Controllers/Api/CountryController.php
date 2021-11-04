<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
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
}
