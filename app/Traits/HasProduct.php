<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Validation\Validator as ValidationObj;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait HasProduct
{
    use StringFormatter;
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
                if ($cat->display_level >= 4) {
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
           $val = Validator::make($variation,$validation_rules);
           if($val->fails()){
              return $this->valResult($val);
           }
        }
        return $this->payload();
     }
}
