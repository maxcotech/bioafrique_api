<?php
namespace App\Actions\StoreStaffToken;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;
use App\Traits\HasRoles;
use App\Traits\HasStore;
use App\Traits\HasStoreRoles;

class GetStoreStaffTokens extends Action{
   use HasRoles,HasStore,HasStoreRoles;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   public function execute(){
      try{
         $user = $this->request->user();
         $select = ['id','staff_token','staff_type','expired','created_at'];
         if($this->isStoreOwner()){
            return $this->successWithData($user->store->staffTokens()->orderBy('id','desc')->paginate(15,$select));
         } else if($this->isStoreManager($user->id,$this->request->query('store',null)) && $this->request->query('store',null) != null) {
            $store = Store::find($this->request->query('store'));
            if(isset($store) && $this->userWorksAtStore($user,$store)){
               return $this->successWithData(
                  $store->staffTokens()->orderBy('id','desc')->paginate(15,$select)
               );
            }
         }
         return $this->validationError("You are not authorized to browse staff tokens in this store.");
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   