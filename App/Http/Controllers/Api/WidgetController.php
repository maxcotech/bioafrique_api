<?php

namespace App\Http\Controllers\Api;

use App\Actions\Widget\DeleteWidget;
use App\Actions\Widget\GetWidgetItems;
use App\Actions\Widget\GetWidgets;
use App\Actions\Widget\SwapWidgetIndex;
use App\Actions\Widget\UpdateWidgetStatus;
use App\Actions\Widget\UploadWidget;
use App\Actions\Widget\UploadWidgetImage;
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
    public function uploadImage(Request $request){
        return (new UploadWidgetImage($request))->execute();
    }
    public function index(Request $request){
        return (new GetWidgets($request))->execute();
    }
    public function updateWidgetStatus(Request $request){
        return (new UpdateWidgetStatus($request))->execute();
    }
    public function swapWidgetIndex(Request $request){
        return (new SwapWidgetIndex($request))->execute();
    }
    public function getWidgetItems(Request $request){
        return (new GetWidgetItems($request))->execute();
    }
    public function deleteWidget(Request $request,$widget_id){
        return (new DeleteWidget($request,$widget_id))->execute();
    }
}
