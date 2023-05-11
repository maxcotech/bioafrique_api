<?php

namespace App\Http\Controllers\Api;

use App\Actions\ContactSupport\ChangeSeenStatus;
use App\Actions\ContactSupport\CreateMessage;
use App\Actions\ContactSupport\GetMessages;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function create(Request $request){
        return (new CreateMessage($request))->execute();
    }

    public function index(Request $request){
        return (new GetMessages($request))->execute();
    }

    public function updateStatus(Request $request){
        return (new ChangeSeenStatus($request))->execute();
    }
}
