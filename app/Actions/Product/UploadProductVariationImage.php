<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ProductVariation;
use App\Traits\FilePath;
use App\Traits\HasFile;
use App\Traits\HasResourceStatus;
use App\Traits\HasStore;

class UploadProductVariationImage extends Action{
   use HasStore,HasResourceStatus,HasFile,FilePath;
   protected $request;
   public const upload_folder = 'product_variation_images';
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'image_file' => 'required|file|mimes:jpg,jpeg,png,gif,webp',
         'store_id' => $this->storeIdValidationRule(),
         'old_image_url' => 'nullable|string',
         'variation_id' => 'nullable|integer|exists:product_variations,id',
         'product_id' => 'nullable|integer|exists:products,id'
      ]);
      return $this->valResult($val);
   }

   protected function updateImageRecord($old_path,$new_path){
      $query = ProductVariation::where('store_id',$this->request->store_id)
      ->where('variation_image',$old_path);
      if($this->request->has('variation_id') && $this->request->variation_id != null){
         $query->where('id',$this->request->variation_id);
      } elseif($this->request->product_id != null){
         $query->where('product_id',$this->request->product_id);
      } else {
         $query->where('variation_status',$this->getResourceInDraftId());
      }
      $query->update([
         'variation_image' => $new_path
      ]);
   }

   protected function createImageRecord($new_path){
      $data = [
            'variation_image' => $new_path,
            'variation_status' => $this->getResourceInDraftId(),
            'store_id' => $this->request->store_id
      ];
      if($this->request->product_id != null){
         $data['product_id'] = $this->request->product_id;
      }
     ProductVariation::create($data);
   }

   protected function variationImgExistsInRecord($url){
      return ProductVariation::where('store_id',$this->request->store_id)
      ->where('variation_image',$url)->exists();
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $new_file_url = $this->uploadImage(
            $this->request->image_file,
            self::upload_folder
         );
         if($this->request->has('old_image_url') && $this->request->old_image_url != null){
            $initial_old_path = $this->getInitialPath($this->request->old_image_path,$this->upload_folder);
            if(isset($initial_old_path) && $this->variationImgExistsInRecord($initial_old_path)){
               $this->updateImageRecord($initial_old_path,$new_file_url);
               $this->deleteFile($initial_old_path);
            } else {
               $this->createImageRecord($new_file_url);
            }
         } else {
            $this->createImageRecord($new_file_url);
         }
         return $this->successWithData([
            'image_full_path' => $this->getRealPath($new_file_url)
         ]);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   