<?php
namespace App\Actions\BillingAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\BillingAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateBillingAddress extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
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
         'first_name' => 'required|string',
         'last_name' => 'required|string',
         'street_address' => 'required|string',
         'postal_code' => 'nullable|string',
         'phone_number' => 'required|integer',
         'telephone_code' => 'required|string|exists:countries,country_tel_code',
         'additional_number' => 'nullable|integer',
         'additional_telephone_code' => 'exclude_if:additional_number,null|required|string'
      ]);
      return $this->valResult($val);
   }

   protected function addressAlreadyExists(){
      return BillingAddress::where('country_id',$this->request->country_id)
      ->where('state_id',$this->request->state_id)
      ->where('city_id',$this->request->city_id)
      ->where('street_address',$this->request->street_address)
      ->where('user_id',$this->user->id)
      ->exists();
   }

   
   protected function removeCurrentStatusFromAddresses(){
      BillingAddress::where('user_id',$this->user->id)
      ->update([
         'is_current' => BillingAddress::$not_current_id
      ]);
   }

   protected function createNewBillingAddress(){
      BillingAddress::create([
         'first_name' => $this->request->first_name,
         'last_name' => $this->request->last_name,
         'country_id' => $this->request->country_id,
         'state_id' => $this->request->state_id,
         'city_id' => $this->request->city_id,
         'street_address' => $this->request->street_address,
         'postal_code' => $this->request->postal_code,
         'telephone_code' => $this->request->telephone_code,
         'phone_number' => $this->request->phone_number,
         'additional_number' => $this->request->additional_number,
         'additional_telephone_code' => $this->request->additional_telephone_code,
         'is_current' => BillingAddress::$current_id,
         'user_id' => $this->user->id
      ]);
   }




   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success" ) return $this->resp($val);
         if($this->addressAlreadyExists()) return $this->validationError('You already have a similar billing address.');
         DB::transaction(function(){
            $this->removeCurrentStatusFromAddresses();
            $this->createNewBillingAddress();
         });
         return $this->successMessage('Your Billing Address was added successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   