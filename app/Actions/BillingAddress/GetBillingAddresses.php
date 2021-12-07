<?php
namespace App\Actions\BillingAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\BillingAddress;
use App\Traits\HasRoles;

class GetBillingAddresses extends Action{
   use HasRoles;
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'user_id' => 'nullable|integer|exists:users,id',
         'limit' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getBillingAddressesByUserId($user_id){
      return BillingAddress::with([
         'state:id,state_name','city:id,city_name',
         'country:id,country_name'])->where('user_id',$user_id)
         ->paginate(
            $this->request->query('limit',15),
            [
               'id','street_address','country_id','state_id','city_id','phone_number',
               'telephone_code','additional_number','additional_telephone_code','postal_code',
               'is_current'
            ]
         );
   }

   protected function isAuthorityUser(){
      $user_type = $this->user->user_type;
      if($this->isStoreStaff($user_type) || $this->isStoreOwner($user_type) || $this->isSuperAdmin($user_type)){
         return true;
      }
      return false;
   }

   protected function onGetBillingAddresses(){
      if($this->isAuthorityUser() && $this->request->query('user_id',null) != null){
         return $this->getBillingAddressesByUserId($this->request->query('user_id'));
      }
      return $this->getBillingAddressesByUserId($this->user->id);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $data = $this->onGetBillingAddresses();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   