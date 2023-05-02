<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductWish;
use App\Models\RecentlyViewed;
use App\Models\SuperAdminPreference;
use Illuminate\Contracts\Validation\Validator as ValidationObj;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait HasProduct
{
    use StringFormatter,FilePath;
    protected $img_labels = [
        'front_image', 'back_image', 'side_image',
        'fourth_image', 'fifth_image', 'sixth_image'
    ];

    protected function dimensionsValidation(ValidationObj $val)
    {
        $val->sometimes('dimension_width', 'required|numeric', function () {
            if ($this->request->weight == null) {
                return true;
            }
            return false;
        });
        $val->sometimes('dimension_height', 'required|numeric', function () {
            if ($this->request->weight == null) {
                return true;
            }
            return false;
        });
        $val->sometimes('dimension_length', 'required|numeric', function () {
            if ($this->request->weight == null) {
                return true;
            }
            return false;
        });
    }

    protected function inferProductType($variations)
    {
        if (isset($variations) && count($variations) > 0) {
            return Product::variation_product_type;
        } else {
            return Product::simple_product_type;
        }
    }

    protected function generateProductSlug($is_update = false,$product_id = null)
    {
        $slug = null;
        $pre_slug = $this->generateSlugFromString($this->request->product_name);
        if ($pre_slug != "") {
            $slug = $pre_slug . "-" . $this->request->store_id;
        }
        if (Product::where('product_slug', $slug)->exists() && $is_update === false) {
            throw new \Exception("it's likely that the product you are trying to create already exists, please check your inventory.");
        } elseif($is_update === true && Product::where('product_slug', $slug)->where('id','!=',$product_id)->exists()) {
            throw new \Exception('The new product name you entered is already being used by another product.');
        } else {
            return $slug;
        }
    }

    protected function validateVariationsRequirements(ValidationObj $val){
        $val->sometimes('variations','required|json',function(){
            if($this->request->product_type != Product::variation_product_type){
               return false;
            } else {
               return true;
            }
         });
    }

    protected function validateGalleryImages(ValidationObj $val){
        $category = Category::where('id',$this->request->category_id)->first();
        foreach($this->img_labels as $label){
            $this->validateGalleryImage($val,$label,$category);
        }
    }


    protected function validateGalleryImage(ValidationObj $val, $param_label, $cat)
    {
        $val->sometimes($param_label, 'required|string', function () use ($param_label, $cat) {
            if (!isset($cat)) return false;
            if ($param_label == 'front_image') {
                if ($cat->display_level >= 1) {
                    return true;
                }
            } else if ($param_label == 'back_image') {
                if ($cat->display_level >= 2) {
                    return true;
                }
            } else if ($param_label == 'side_image') {
                if ($cat->display_level >= 3) {
                    return true;
                }
            } else if ($param_label == 'fourth_image' || $param_label == 'fifth_image' || $param_label == 'sixth_image') {
                if ($cat->display_level >= 10) {
                    return true;
                }
            }
            return false;
        });
    }

    protected function validateVariations($variations,$product_id = null){
        $validation_rules = [
           'variation_image_url' => ['required','string',Rule::exists('product_variations','variation_image')
           ->where(function($query)use($product_id){ return $query->where('product_id',$product_id);})],
           'variation_name' => 'required|string',
           'variation_sku' => 'nullable|string',
           'regular_price' => 'required|numeric',
           'sales_price' => 'nullable|numeric',
           'variation_id' => ['nullable','integer',Rule::exists('product_variations','id')
                ->where(function($query)use($product_id){
                    return $query->where('product_id',$product_id);
                })
            ]
        ];
       
        if(count($variations) < 1) return $this->valMessageObject('At least one variation required for variation products.');
        foreach($variations as $variation){
           Log::alert('variation image '.$this->getInitialPath($variation['variation_image_url'],'product_variation_images'));
           $variation['variation_image_url'] = $this->getInitialPath($variation['variation_image_url'],'product_variation_images');
           $val = Validator::make($variation,$validation_rules);
           if($val->fails()){
              return $this->valResult($val);
           }
        }
        return $this->payload();
    }

    protected function getProductComissionFee($product_id){
        $product = Product::find($product_id);
        if(!isset($product)) throw new \Exception('Invalid product was selected');
        $category = $product->category;
        if(isset($category) && $category->commission_fee != null){
            return $category->commission_fee;
        }
        $pref_key = SuperAdminPreference::COMMISSION_PREFERENCE;
        $default_fee = SuperAdminPreference::where('preference_key',$pref_key)->first();
        if(isset($default_fee)){
            return $default_fee->preference_value;
        }
        return 0.5;
    }

    protected function getOrderItemDetails($order_items){
        //$order_items = json_decode(json_encode($order_items));
        $output = [];
        foreach($order_items as $item){
            $item->product = $item->product()->select('product_name','product_image')->first();
            $item->variation = $item->variation()->select('variation_name','variation_image')->first();
            array_push($output,$item);
        }
        return $output;
    }

    protected function appendWishListStatus($data,$access_type,$product_key = "id"){
        $user_id = $access_type->id;
        $user_type = $access_type->type;
        $product_ids = json_decode(json_encode(ProductWish::where('user_type',$user_type)->where('user_id',$user_id)->pluck('product_id')),true);
        if(count($data) > 0){
           $data->each(function($item) use($product_ids,$product_key){
               $item_array = json_decode(json_encode($item),true);
               $item->in_wishlist = in_array($item_array[$product_key],$product_ids);
               return $item;
           });
        }
        return $data;
    }

    protected function addToRecentlyViewed($product_id,$access_type){
        $user_type = $access_type->type;
        $user_id = $access_type->id;
        $record_exists = RecentlyViewed::where('user_type',$user_type)->where('user_id',$user_id)->where('product_id',$product_id)->exists();
        if($record_exists){
            RecentlyViewed::where('user_type',$user_type)->where('user_id',$user_id)->where('product_id',$product_id)
            ->update(['last_viewed' => now()->format('Y-m-d H:i:s')]);
        } else {
            RecentlyViewed::create([
                'product_id' => $product_id,
                'user_type' => $user_type,
                'user_id' => $user_id,
                'last_viewed' => now()->format('Y-m-d H:i:s')
            ]);
        }
    }
     
}
