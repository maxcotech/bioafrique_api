<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Traits\FilePath;
use App\Traits\HasFile;
use App\Traits\HasResourceStatus;
use App\Traits\HasStore;

class UploadProductImage extends Action{
   use HasStore,FilePath,HasFile,HasResourceStatus;
   protected $request;
   protected $upload_folder = "product_images";
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'image_file' => 'required|file|mimes:jpg,jpeg,gif,png,webp',
         'store_id' => $this->storeIdValidationRule(),
         'product_id' => 'nullable|integer|exists:products,id',
         'old_image_url' => 'nullable|string'
      ]);
      return $this->valResult($val);
   }

   public function getUploadFolder(){
      return $this->upload_folder;
   }

   protected function createImageRecord($file_url){
      $data = [
         'product_image' => $file_url,
         'product_status' => $this->getResourceInDraftId(),
         'store_id' => $this->request->store_id
      ];
      Product::create($data);
   }

   protected function updateImageRecord($old_file,$new_file){
      $query = Product::where('product_image',$old_file)
      ->where('store_id',$this->request->store_id);
      if($this->request->has('product_id') && $this->request->product_id != null){
         $query->where('id',$this->request->product_id);
      } else {
         $query->where('product_status',$this->getResourceInDraftId());
      }
      $query->update([
         'product_image' => $new_file
      ]);
   }

   protected function oldImageExistsInRecord($url){
      return Product::where(
         'product_image',
         $url
      )
      ->where('store_id',$this->request->store_id)
      ->exists();
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != 'success') return $this->resp($val);
         $new_file_url = $this->uploadImage($this->request->image_file,$this->upload_folder);
         if($this->request->has('old_image_url') && $this->request->old_image_url != null ){
            $initial_old_path = $this->getInitialPath($this->request->old_image_url,$this->upload_folder);
            if(isset($initial_old_path) && $this->oldImageExistsInRecord($initial_old_path)){
               $this->updateImageRecord($initial_old_path,$new_file_url);
               $this->deleteFile($initial_old_path);
            } else {
               $this->createImageRecord($new_file_url);
            }
         } else {
            $this->createImageRecord($new_file_url);
         }
         return $this->successWithData([
            'image_full_path' => $this->getRealPath($new_file_url),
         ]);

      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   