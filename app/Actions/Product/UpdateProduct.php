<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\VariationAttribute;
use App\Traits\FilePath;
use App\Traits\HasProduct;
use App\Traits\HasRateConversion;
use App\Traits\HasStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateProduct extends Action{
   use HasStore,HasProduct,FilePath,HasRateConversion;

   protected $request,$user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $this->request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'product_id' => ['required','integer',Rule::exists('products','id')
         ->where(function($query){
               return $query->where('store_id',$this->request->store_id);
            })
         ],
         'product_name' => 'required|string',
         'product_sku' => 'nullable|string',
         'regular_price' => 'required|numeric',
         'sales_price' => 'nullable|numeric',
         'simple_description' => 'required|string',
         'description' => 'nullable|string',
         'amount_in_stock' => 'required|numeric',
         'category_id' => 'required|integer|exists:categories,id',
         'brand_id'=>'required|integer|exists:brands,id',
         'youtube_video_id'=>'nullable|string',
         'product_type' => 'nullable|integer',
         'weight' => 'nullable|numeric',
         'key_features' => 'nullable|string'
      ]);
      $this->dimensionsValidation($val);
      $this->validateVariationsRequirements($val);
      return $this->valResult($val);
   }

   protected function updateProduct($variations){
      Product::where('store_id',$this->request->store_id)
      ->where('id',$this->request->product_id)
      ->update([
         'product_name' => $this->request->product_name,
         'product_sku' => $this->request->product_sku,
         'regular_price' => $this->userToBaseCurrency($this->request->regular_price,$this->user),
         'sales_price' => $this->userToBaseCurrency($this->request->sales_price,$this->user),
         'simple_description' => htmlspecialchars($this->request->simple_description),
         'description' => htmlspecialchars($this->request->description),
         'key_features' => htmlspecialchars($this->request->key_features),
         'amount_in_stock' => $this->request->amount_in_stock,
         'category_id' => $this->request->category_id,
         'brand_id' => $this->request->brand_id,
         'youtube_video_id' => $this->request->youtube_video_id,
         'product_type' => $this->request->product_type ?? $this->inferProductType($variations),
         'weight' => $this->request->weight,
         'dimension_height' => $this->request->dimension_height,
         'dimension_width' => $this->request->dimension_width,
         'dimension_length' => $this->request->dimension_length,
         'product_slug' => $this->generateProductSlug(true,$this->request->product_id),
      ]);
   }

   protected function deleteExcludedVariations($variations){
      $conserved = [];
      $count = 0;
      foreach($variations as $variation){
         $count = array_push($conserved,$variation['id']);
      }
      if($count > 0){
         $excluded_variations = ProductVariation::where('store_id',$this->request->store_id)
         ->where('product_id',$this->request->product_id)
         ->whereNotIn('id',$conserved)->get();
         if(isset($excluded_variations) && count($excluded_variations) > 0){
            foreach($excluded_variations as $ex_variation){
               VariationAttribute::where('variation_id',$ex_variation->id)->delete();
               $ex_variation->delete();
            }
         }
      }
   }

   protected function updateProductVariations($variations){
      if(isset($variations) && count($variations) > 0){
         $this->deleteExcludedVariations($variations);
         foreach($variations as $variation){
            $init_val_img = $this->getInitialPath($variation['variation_image_url'],'product_variation_images');
            $filters = [
               'product_id' => $this->request->product_id,
               'variation_image' => $init_val_img,
               'store_id' => $this->request->store_id,
            ];
            if(isset($variation['variation_id'])) $filters['id'] = $variation['variation_id'];
            $parameters = [
               'variation_name' => $variation['variation_name'] ?? null,
               'variation_sku' => $variation['variation_sku'] ?? null,
               'regular_price' => $variation['regular_price'] ?? null,
               'sales_price' => $variation['sales_price'] ?? null,
               'amount_in_stock' => $variation['amount_in_stock'] ?? null,
            ];
            $variation_record = ProductVariation::updateOrCreate($filters,$parameters);
            $this->updateVariationAttributes($variation,$variation_record);
         }
      }
   }

   protected function updateVariationAttributes($variation,$var_record){
      if(isset($var_record) && $var_record->id != null){
         $variation_options = $variation['options'] ?? [];
         if(isset($variation_options) && count($variation_options) > 0){
            foreach($variation_options as $option){
               if(isset($option['id'])){
                  VariationAttribute::updateOrCreate([
                     'id' => $option['id'],'variation_id' => $var_record->id,
                  ],[
                     'option_id' => $option['option_id'],'option_value' => $option['option_value']
                  ]);
               } else {
                  VariationAttribute::create([
                     'variation_id' => $var_record->id, 'option_id' => $option['option_id'],
                     'option_value' => $option['option_value']
                  ]);
               }
            }
         }
      }
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $variations = ($this->request->variations != null)? json_decode($this->request->variations,true):[];
         if(isset($variations) && $this->request->variations != null){
            $val2 = $this->validateVariations($variations,$this->request->product_id);
            if($val2['status'] != "success") return $this->resp($val2);
         }
         DB::transaction(function()use($variations){
            $this->updateProduct($variations);
            if($this->request->variations != null){
               $this->updateProductVariations($variations);
            }
         });
         return $this->successMessage('Product update was successfully completed.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   