<?php
namespace App\Actions\StoreStaffToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Http\Resources\StoreStaffToken;

class GetStoreStaffTokens extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   public function execute(){
      try{
         $user = $this->request->user();
         $tokens = $user->store->staffTokens()->paginate();
         return $this->successWithData(StoreStaffToken::collection($tokens));
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   