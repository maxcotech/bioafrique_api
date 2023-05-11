<?php
namespace App\Actions\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\User;

class UpdateUserStatus extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'user_id' => 'required|integer|exists:users,id',
         'status' => 'required|integer'
      ]);
      return $this->valResult($val);
   }
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         User::where('id',$this->request->user_id)
         ->update(['account_status'=> $this->request->status]);
         return $this->successMessage('User status successfully updated');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   