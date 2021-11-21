<?php

namespace App\Actions\Store;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;
use App\Traits\FilePath;
use App\Traits\HasFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UploadStoreLogo extends Action
{
   use FilePath,HasFile;
   protected $request;
   public function __construct(Request $request)
   {
      $this->request = $request;
      $this->user = Auth::user();
   }
   protected function validate()
   {
      $val = Validator::make($this->request->all(), [
         'store_logo' => 'required|file|mimes:jpg,jpeg,png,gif,webp',
         'store_id' => 'required|integer|exists:stores,id'
      ]);
      return $this->valResult($val);
   }

   protected function deletePreviousLogo($store)
   {
      if ($store->store_logo != null) {
         Storage::disk(env('CURRENT_DISK'))->delete(
            $this->getInitialPath($store->store_logo, 'stores')
         );
      }
   }

   protected function getStore(){
      return Store::where('id',$this->request->store_id)
      ->where('user_id',$this->user->id)
      ->first();
   }

   public function execute()
   {
      try {
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $store = $this->getStore();
         $new_path = $this->uploadImage($this->request->store_logo,'stores');
         $this->deletePreviousLogo($store);
         $store->update(['store_logo' => $new_path]);
         return $this->successWithData(
            $this->getRealPath($new_path)
         ,
            'Store Logo Successfully uploaded.'
         );

      } catch (\Exception $e) {
         return $this->internalError($e->getMessage());
      }
   }
}
