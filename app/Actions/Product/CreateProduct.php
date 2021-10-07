<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Traits\FilePath;
use App\Traits\HasFile;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;
use App\Traits\HasStore;
use App\Traits\HasUserStatus;
use App\Traits\StringFormatter;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateProduct extends Action{
   use HasFile,FilePath,HasStore,HasResourceStatus,StringFormatter,HasRoles,HasUserStatus;
   protected $request;
   protected $img_labels = [
      'front_image','back_image','side_image',
      'fourth_image','fifth_image','sixth_image'
   ];

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
         'main_product_image'=>'required|string',
         'brand_id'=>'required|integer|exists:brands,id',
         'youtube_video_id'=>'nullable|string',
         'product_type' => 'nullable|integer',
         'weight' => 'nullable|numeric',
         'key_features' => 'nullable|string'
      ]);
      $category = Category::where('id',$this->request->category_id)->first();
      foreach($this->img_labels as $label){
         $this->validateGalleryImage($val,$label,$category);
      }
      $val->sometimes('variations','required|json',function(){
         if($this->request->product_type != Product::variationProductType){
            return false;
         } else {
            return true;
         }
      });
      $this->dimensionsValidation($val);
      return $this->valResult($val);
   }

   protected function dimensionsValidation($val){
      $val->sometimes('dimension_width','required|numeric',function(){
         if($this->request->weight == null){
            return true;
         } 
         return false;
      });
      $val->sometimes('dimension_height','required|numeric',function(){
         if($this->request->weight == null){
            return true;
         }
         return false;
      });
      $val->sometimes('dimension_length','required|numeric',function(){
         if($this->request->weight == null){
            return true;
         }
         return false;
      });
   }

   protected function inferProductType($variations){
      if(isset($variations) && count($variations) > 0){
         return Product::variationProductType;
      } else {
         return Product::simpleProductType;
      }
   }

   protected function generateProductSlug(){
      $pre_slug = $this->generateSlugFromString($this->request->product_name);
      if($pre_slug != ""){
         $slug = $pre_slug . "-" . $this->request->store_id;
      }
      if(Product::where('product_slug',$slug)->exists()){
         throw new \Exception("it's likely that the product you are trying to create already exists, please check your inventory.");
      } else {
         return $slug;
      }
   }

   protected function createProduct($image_url,$variations){
      if(!isset($image_url)) throw new \Exception('invalid image path uploaded.');
      $product_obj = Product::updateOrCreate([
         'product_image' => $image_url,
         'store_id' => $this->request->store_id,
         'product_status',$this->getResourceInDraftId()
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
         'product_type' => $this->request->product_type ?? $this->inferProductType($variations),
         'product_slug' => $this->generateProductSlug(),
         'dimension_height' => $this->request->dimension_height,
         'dimension_width' => $this->request->dimension_width,
         'dimension_length' => null
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
               ProductVariation::where('store_id',$this->request->store_id)
               ->where('variation_image',$init_val_img)
               ->where('variation_status',$this->getResourceInDraftId())
               ->where('product_id',null)
               ->update([
                  'product_id' => $product->id,
                  'variation_status' => $this->getResourceInReviewId()
               ]);
            }
         }
      }
   }

   protected function validateVariations($variations){
      if(count($variations) < 1) return $this->valMessageObject('At least one variation required for variation products.');
      foreach($variations as $variation){
         $val = Validator::make($variation,[
            'variation_image_url' => ['required','string',Rule::exists('product_variations','variation_image')
            ->where(function($query){
               return $query->where('product_id',null);
            })],
            'variation_name' => 'required|string',
            'variation_sku' => 'nullable|string',
            'regular_price' => 'required|numeric',
            'sales_price' => 'nullable|numeric'
         ]);
         if($val->fails()){
            return $this->valResult($val);
         }
      }
      return $this->payload();
   }
   
   protected function validateGalleryImage(ValidationValidator $val,$param_label,$cat){
      $val->sometimes($param_label,'required|string',function($input) use($param_label,$cat){
         if(!isset($cat)) return false;
         if($param_label == 'front_image'){
            if($cat->display_level >= 1){ return true;}
         } else if($param_label == 'back_image'){
            if($cat->display_level >= 2){ return true;}
         } else if($param_label == 'side_image'){
            if($cat->display_level >= 3){ return true;}
         } else if($param_label == 'fourth_image' || $param_label == 'fifth_image' || $param_label == 'sixth_image'){
            if($cat->display_level >= 4){ return true;}
         } 
         return false;
      });
   }

   public function execute(){
      try{
         $val = $this->validate(); 
         if($val['status'] != "success") return $this->resp($val);
         $variations = ($this->request->variations != null)? json_decode($this->request->variations):null;
         $main_image_url = $this->getInitialPath($this->request->main_product_image,'product_images');
         if(isset($variations)){
            $val2 = $this->validateVariations($variations);
            if($val2['status'] != "success") return $this->resp($val2);
         }
         /*
         DB::transaction(function()use($main_image_url,$variations){
            $product = $this->createProduct($main_image_url,$variations);
            if(isset($product) && $product->id != null){
               $this->saveGalleryImages($product);
               $this->saveProductVariations($product,$variations);
            } else {
               throw new \Exception('Failed to complete product upload, please try again or contact admin');
            }
         });*/
        //return $this->successMessage('Product successfully uploaded to store, currently awaiting review.');
        return $this->successMessage($this->generateProductSlug());

      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   