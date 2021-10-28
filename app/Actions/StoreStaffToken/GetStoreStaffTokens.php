<?php
namespace App\Actions\StoreStaffToken;
use Illuminate\Http\Request;
use App\Actions\Action;


class GetStoreStaffTokens extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   public function execute(){
      try{
         $user = $this->request->user();
         $data = $user->store->staffTokens()->paginate(15,['id','staff_token','staff_type']);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   