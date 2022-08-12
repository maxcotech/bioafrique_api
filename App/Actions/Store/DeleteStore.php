<?php
namespace App\Actions\Store;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Actions\Product\UploadGalleryImage;
use App\Actions\Product\UploadProductImage;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\StoreStaff;
use App\Traits\FilePath;
use App\Traits\HasFile;
use App\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class DeleteStore extends Action{
   use HasRoles, HasFile, FilePath;
   protected $request;
   protected $user;
   protected $store_id;
   public function __construct(Request $request,$store_id){
      $this->request=$request;
      $this->user = $request->user();
      $this->store_id = $store_id;
   }

   protected function userIsEligible(){
      $user_type = $this->user->user_type;
      if($this->isStoreOwner($user_type)){
         if(Store::where('id',$this->store_id)->where('user_id',$this->user->id)->exists()){
            return true;
         }
         return false;
      } elseif($this->isSuperAdmin($user_type)){
         return true;
      } else {
         return false;
      }
   }

   protected function deleteStoreProducts(){
      $product_images = ProductImage::where("store_id",$this->store_id)->get();
      if(count($product_images) > 0){
         $gallery_uploader = new UploadGalleryImage($this->request);
         foreach($product_images as $product_image){
            $init_path = $this->getInitialPath($product_image->image_url,$gallery_uploader->getUploadFolder());
            $this->deleteFile($init_path);
         }
         ProductImage::where("store_id",$this->store_id)->delete();
      }
      $products = Product::where('store_id',$this->store_id)->get();
      if(count($products) > 0){
         $image_uploader = new UploadProductImage($this->request);
         foreach($products as $product){
            $init_path = $this->getInitialPath($product->product_image,$image_uploader->getUploadFolder());
            $this->deleteFile($init_path);
         }
         Product::where('store_id',$this->store_id)->delete();
      }
   }

  
   public function execute(){
      try{
         if($this->userIsEligible()){
            DB::transaction(function(){
               $this->deleteStoreProducts();
               StoreStaff::where("store_id",$this->store_id)->delete();
               Store::where('id',$this->store_id)->delete();
            });
            return $this->successMessage('Store account was deleted successfully.');
         } 
         return $this->notAuthorized("You are not authorized to carry out this operation.");
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   