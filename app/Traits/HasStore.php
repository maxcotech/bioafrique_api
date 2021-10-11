<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

trait HasStore{
   use HasUserStatus,HasRoles;

   protected function storeIdValidationRule(){
      $data = ['required','integer'];
      $user = $this->request->user();
      if($this->isStoreOwner()){
         array_push($data,Rule::exists('stores','id')->where(function($query)use($user){
            return $query->where('user_id',$user->id)->where('store_status',$this->getActiveUserId());
         }));
      } else {
         array_push($data,Rule::exists('store_staffs','store_id')->where(function($query)use($user){
            return $query->where('user_id',$user->id)->where('status',$this->getActiveUserId());
         }));
      }
      return $data;
   }

   protected function userHasStore($user = null){
      $user_acct = isset($user)? $user:Auth::user();
      if(isset($user_acct)){
         if($this->isStoreOwner()){
            return $user_acct->store()->where('store_status',$this->getActiveUserId())->exists();
         } else {
            return $user_acct->workStores()->where('status',$this->getActiveUserId())->exists();
         }
      } 
      return false;
   }

   
}
   