<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ProductImage;
use App\Traits\FilePath;
use App\Traits\HasFile;
use App\Traits\HasResourceStatus;
use App\Traits\HasStore;

class UploadGalleryImage extends Action{
   use HasFile,FilePath,HasStore,HasResourceStatus;
   protected $request;
   protected $user;
   protected $upload_folder = "product_gallery";
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $this->request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'image_file' => 'required|file|mimes:jpg,png,jpeg,gif,webp',
         'old_image_url' => 'nullable|string',
         'image_type' => 'required|string',
         'product_id' => 'nullable|integer|exists:products,id'
      ]);
      return $this->valResult($val);
   }

   protected function createNewRecord($url){
      $data = [
         'image_type' => $this->request->image_type,
         'image_url' => $url,
         'store_id' => $this->request->store_id
      ];
      if($this->request->has('product_id') && $this->request->filled('product_id') && $this->request->product_id != null){
         $data['product_id'] = $this->request->product_id;
      }
      ProductImage::create($data);
   }

   protected function updateOldImageRecord($old_path,$new_path){
      $query = ProductImage::where('image_url',$old_path)
      ->where('image_type',$this->request->image_type)
      ->where('store_id',$this->request->store_id);
      if($this->request->has('product_id') && $this->request->filled('product_id') && $this->request->product_id != null){
         $query->where('product_id',$this->request->product_id);
      } else {
         $query->where('product_id',null);
      }
      $query->update([
         'image_url' => $new_path
      ]);
      
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $new_image_url = $this->uploadImage($this->request->image_file,$this->upload_folder);
         if($this->request->has('old_image_url') && $this->request->filled('old_image_url')){
            $old_image_url = $this->request->old_image_url;
            $initial_old_image_path = $this->getInitialPath($old_image_url,$this->upload_folder);
            $this->updateOldImageRecord($initial_old_image_path,$new_image_url);
            $this->deleteFile($initial_old_image_path);
         } else {
            $this->createNewRecord($new_image_url);
         }
         return $this->successWithData([
            'image_full_path' => $this->getRealPath($new_image_url),
            'image_type' => $this->request->image_type
         ]);
         
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   