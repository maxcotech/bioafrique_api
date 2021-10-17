<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

trait HasStore{
   use HasRoles,HasResourceStatus;

   protected function storeIdValidationRule(){
      $data = ['required','integer'];
      $user = $this->request->user();
      if($this->isStoreOwner()){
         array_push($data,Rule::exists('stores','id')->where(function($query)use($user){
            return $query->where('user_id',$user->id)->where('store_status',$this->getResourceActiveId());
         }));
      } else {
         array_push($data,Rule::exists('store_staffs','store_id')->where(function($query)use($user){
            return $query->where('user_id',$user->id)->where('status',$this->getResourceActiveId());
         }));
      }
      return $data;
   }

   protected function userHasStore($user = null){
      $user_acct = isset($user)? $user:Auth::user();
      if(isset($user_acct)){
         if($this->isStoreOwner()){
            return $user_acct->store()->where('store_status',$this->getResourceActiveId())->exists();
         } else {
            return $user_acct->workStores()->where('status',$this->getResourceActiveId())->exists();
         }
      } 
      return false;
   }

   protected function generateStoreStaffToken($store_id){
      if(isset($store_id)){
         $random_int = random_int(10000000,900000000);
         $hash = Hash::make($random_int);
         return $store_id.$hash;
      }
      return null;
   }

   
}
   