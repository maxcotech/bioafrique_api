<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Models\VariationAttribute;
use App\Traits\FilePath;
use App\Traits\HasFile;
use App\Traits\HasProduct;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;
use App\Traits\HasStore;
use App\Traits\HasUserStatus;
use Illuminate\Support\Facades\DB;

class CreateProduct extends Action{
   use HasFile,FilePath,HasStore,HasResourceStatus,HasRoles,HasUserStatus,HasProduct;
   protected $request;
   

   public function __construct(Request $request){
      $this->request=$request;
   }
   protected function validate(){
      $val = validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'product_name' => 'required|string',
         'product_sku' => 'nullable|string',
         'regular_price' => 'required|numeric',
         'sales_price' => 'nullable|numeric',
         'simple_description' => 'required|string',
         'description' => 'nullable|string',
         'amount_in_stock' => 'required|numeric',
         'category_id' => 'required|integer|exists:categories,id',
         'product_image'=>'required|string',
         'brand_id'=>'required|integer|exists:brands,id',
         'youtube_video_id'=>'nullable|string',
         'weight' => 'nullable|numeric',
         'key_features' => 'nullable|string',
         'variations' => 'nullable|json'
      ]);
      $this->validateGalleryImages($val);
      $this->dimensionsValidation($val);
      return $this->valResult($val);
   }


   protected function createProduct($image_url,$variations){
      if(!isset($image_url)) throw new \Exception('invalid image path uploaded.');
      $product_obj = Product::updateOrCreate([
         'product_image' => $image_url,
         'store_id' => $this->request->store_id,
         'product_status' => $this->getResourceInDraftId()
      ],[
         'product_name' => $this->request->product_name,
         'product_sku' => $this->request->product_sku,
         'regular_price' => $this->request->regular_price,
         'sales_price' => $this->request->sales_price,
         'simple_description' => htmlspecialchars($this->request->simple_description),
         'description' => htmlspecialchars($this->request->description),
         'key_features' => htmlspecialchars($this->request->key_features),
         'amount_in_stock' => $this->request->amount_in_stock,
         'category_id' => $this->request->category_id,
         'product_status' => $this->getResourceInReviewId(),
         'brand_id' => $this->request->brand_id,
         'youtube_video_id' => $this->request->youtube_video_id,
         'product_type' => $this->inferProductType($variations),
         'product_slug' => $this->generateProductSlug(),
         'dimension_height' => $this->request->dimension_height,
         'dimension_width' => $this->request->dimension_width,
         'dimension_length' => $this->request->dimension_length,
         'weight' => $this->request->weight
      ]);
      return $product_obj;
   }


   protected function saveGalleryImages($product){
      $req_array = $this->request->all();
      foreach($this->img_labels as $label){
         $img_url = $req_array[$label] ?? null;
         if(isset($img_url)){
            $initial_url = $this->getInitialPath($img_url,'product_gallery');
            if(isset($initial_url)){
               ProductImage::where('image_url',$initial_url)
               ->where('store_id',$this->request->store_id)
               ->where('product_id',null)
               ->where('image_type',$label)->update([
                  'product_id' => $product->id
               ]);
            }
         }
      }
   }

   protected function saveProductVariations($product,$variations){
      if(isset($variations) && count($variations) > 0){
         foreach($variations as $variation){
            $init_val_img = $this->getInitialPath($variation['variation_image_url'],'product_variation_images');
            if(isset($init_val_img)){
               $var_record = ProductVariation::updateOrCreate([
                  'store_id' => $this->request->store_id,
                  'variation_image' => $init_val_img,
                  'product_id' => null,
                  'variation_status' => $this->getResourceInDraftId()
               ],[
                  'product_id' => $product->id,
                  'variation_name' => $variation['variation_name'] ?? null,
                  'variation_sku' => $variation['variation_sku'] ?? null,
                  'regular_price' => $variation['regular_price'] ?? null,
                  'sales_price' => $variation['sales_price'] ?? null,
                  'amount_in_stock' => $variation['amount_in_stock'] ?? null,
                  'variation_status' => $this->getResourceInReviewId() 
               ]);
               $this->uploadVariationAttributes($variation,$var_record);
            }
         }
      }
   }

   protected function uploadVariationAttributes($variation,$var_record){
      if(isset($var_record) && $var_record->id != null){
         $variation_options = $variation['options'] ?? [];
         if(isset($variation_options) && count($variation_options)){
            foreach($variation_options as $option){
               VariationAttribute::create([
                  'variation_id' => $var_record->id,
                  'option_id' => $option['option_id'],
                  'option_value' => $option['option_value']
               ]);
            }
         }
      }
   }

   
   public function execute(){
      try{
         $val = $this->validate(); 
         if($val['status'] != "success") return $this->resp($val);
         $variations = ($this->request->variations != null)? json_decode($this->request->variations):[];
         $main_image_url = $this->getInitialPath($this->request->product_image,'product_images');
         if(isset($variations) && count($variations) > 0 && $this->request->variations != null){
            $val2 = $this->validateVariations($variations);
            if($val2['status'] != "success") return $this->resp($val2);
         }
         
         DB::transaction(function()use($main_image_url,$variations){
            $product = $this->createProduct($main_image_url,$variations);
            if(isset($product) && $product->id != null){
               $this->saveGalleryImages($product);
               $this->saveProductVariations($product,$variations);
            } else {
               throw new \Exception('Failed to complete product upload, please try again or contact admin');
            }
         });
        return $this->successMessage('Product successfully uploaded to store, currently awaiting review.');

      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   