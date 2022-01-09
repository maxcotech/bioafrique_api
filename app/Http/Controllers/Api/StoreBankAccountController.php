<?php

namespace App\Http\Controllers\Api;

use App\Actions\StoreBankAccount\CreateAccount;
use App\Actions\StoreBankAccount\DeleteAccount;
use App\Actions\StoreBankAccount\GetAccounts;
use App\Actions\StoreBankAccount\UpdateAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreBankAccountController extends Controller
{
    public function create(Request $request){
        return (new CreateAccount($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateAccount($request))->execute();
    }
    public function index(Request $request){
        return (new GetAccounts($request))->execute();
    }
    public function delete(Request $request,$account_id){
        return (new DeleteAccount($request,$account_id))->execute();
    }
}
