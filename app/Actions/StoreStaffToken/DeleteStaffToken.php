<?php
namespace App\Actions\StoreStaffToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreStaffToken;

class DeleteStaffToken extends Action{
   protected $request;
   protected $id;

   public function __construct(Request $request,$id){
      $this->request=$request;
      $this->id = $id;
   }

   protected function validate(){
      $val = Validator::make(['store_staff_token_id'=>$this->id],[
         'store_staff_token_id'=>'required|integer|exists:store_staff_tokens,id'
      ]);
      return $this->valResult($val);
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != 'success') return $this->resp($val);
         StoreStaffToken::where('id',$this->id)->delete();
         return $this->successMessage('Successfully deleted staff token');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   