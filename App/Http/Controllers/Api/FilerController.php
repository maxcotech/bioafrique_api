<?php

namespace App\Http\Controllers\Api;

use App\Actions\Filer\CreateFiler;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FilerController extends Controller
{
    public function create(Request $request){
        return (new CreateFiler($request))->execute();
    }
}
