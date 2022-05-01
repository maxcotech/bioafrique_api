<?php

namespace App\Http\Controllers\Api;

use App\Actions\Search\SearchResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request,$search_type){
        return (new SearchResource($request,$search_type))->execute();
    }
}
