<?php
namespace App\Traits;

use App\Models\StoreStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

trait HasStore{
   use HasRoles,HasResourceStatus,HasUserStatus;

   protected function storeIdValidationRule(){
      $data = ['required','integer'];
      $user = $this->request->user();
      if($this->isStoreOwner()){
         array_push($data,Rule::exists('stores','id')->where(function($query)use($user){
            return $query->where('user_id',$user->id)->where('store_status',$this->getResourceActiveId());
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
            return $user_acct->store()->where('store_status',$this->getResourceActiveId())->exists();
         } else {
            return $user_acct->workStores()->where('status',$this->getResourceActiveId())->exists();
         }
      } 
      return false;
   }

   protected function userWorksAtStore($user,$store){
      if(StoreStaff::where('store_id',$store->id)->where('user_id',$user->id)
      ->where('status',$this->getActiveUserId())->exists()){
         return true;
      } else {
         return false;
      }
   }

   protected function generateStoreStaffToken($store_id){
      if(isset($store_id)){
         $random_int = random_int(10000000000000,90000000000000000);
         return $store_id.$random_int;
      }
      return null;
   }

   protected function getStoreIndexFromRequest(Request $request){
      if($request->query('store_id',null) != null){
         return [
            'value' => $request->query('store_id'),
            'key' => 'id'
         ];
      } else if($request->store_id != null){
         return [
            'value' => $request->store_id,
            'key' => 'id'
         ];
      } else if($request->query('store',null) != null){
         return [
            'value' => $request->query('store'),
            'key' => 'id'
         ];
      } else if($request->query('store_id',null) != null){
         return [
            'value' => $request->query('store_id'),
            'key' => 'id'
         ];
      } else if($request->store != null){
         return [
            'value' => $request->store,
            'key' => 'id'
         ];
      }
      else if($request->query('store_slug',null) != null){
         return [
            'value' => $request->query('store_slug'),
            'key' => 'store_slug'
         ];
      } else if($request->store_slug != null){
         return [
            'value' => $request->store_slug,
            'key' => 'store_slug'
         ];
      } else if($request->route('store_id',null) != null){
         return [
            'value' => $request->route('store_id'),
            'key' => 'id'
         ];
      } else if($request->route('store_slug',null) != null){
         return [
            'value' => $request->route('store_slug'),
            'key' => 'store_slug'
         ];
      }
      else { return null; }

   }

   
}
   