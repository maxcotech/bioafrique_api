<?php

namespace App\Http\Controllers\Api;

use App\Actions\Widget\UploadWidget;
use App\Actions\Widget\UploadWidgetItems;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function upload(Request $request){
        return (new UploadWidget($request))->execute();
    }
    public function uploadItems(Request $request){
        return (new UploadWidgetItems($request))->execute();
    }
}
