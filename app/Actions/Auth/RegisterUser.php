<?php
namespace App\Actions\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUser extends Action{
    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
    }
    protected function validate(){
       $val = Validator::make($this->request->all(),[
         'first_name' => 'required|string',
         'last_name' => 'required|string',
         'phone_number' => 'required|numeric|min:10',
         'email' => 'required|email|unique:users,email',
         'password' => 'required|string',
         'confirm_password' => 'required|same:password'
       ]);
       return $this->valResult($val);
    }
    protected function createUser(){
       User::create([
         'first_name' => $this->request->first_name,
         'last_name' => $this->request->last_name,
         'phone_number' => $this->request->phone_number,
         'email' => $this->request->email,
         'password' => Hash::make($this->request->password),
         'auth_type' => 0,
       ]);
    }
   
    public function execute(){
       try{
          $val = $this->validate();
          if($val['status'] != "success") return $this->resp($val);
          $this->createUser();
          return $this->successMessage('Your account was successfully created.');
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    