<?php
namespace App\Actions\BillingAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\BillingAddress;
use Illuminate\Validation\Rule;

class UpdateBillingAddress extends Action{
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => ['required','integer',Rule::exists('billing_addresses','id')->where(function($query){
            return $query->where('user_id',$this->user->id);
         })],
         'country_id' => 'required|integer|exists:countries,id',
         'state_id' => ['required','integer',Rule::exists('states','id')
         ->where(function($query){
            return $query->where('country_id',$this->request->country_id);
         })],
         'city_id' => ['required','integer',Rule::exists('cities','id')
         ->where(function($query){
            return $query->where('state_id',$this->request->state_id);
         })
          ],
         'street_address' => 'required|string',
         'postal_code' => 'nullable|string',
         'phone_number' => 'required|integer',
         'telephone_code' => 'required|string|exists:countries,country_tel_code',
         'additional_number' => 'nullable|integer',
         'additional_telephone_code' => 'exclude_if:addtional_number,null|required|string'
      ]);
      return $this->valResult($val);
   }

   protected function addressAlreadyExists(){
      return BillingAddress::where('id','!=',$this->request->id)
      ->where('country_id',$this->request->country_id)
      ->where('state_id',$this->request->state_id)
      ->where('city_id',$this->request->city_id)
      ->where('street_address',$this->request->street_address)
      ->where('user_id',$this->user->id)
      ->exists();
   }

   protected function onUpdateBillingAddress(){
      BillingAddress::where('id',$this->request->id)
      ->where('user_id',$this->user->id)
      ->update($this->request->all());
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         if($this->addressAlreadyExists()) return $this->validationError('You already have a similar billing address');
         $this->onUpdateBillingAddress();
         return $this->successMessage('Billing address updated successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   