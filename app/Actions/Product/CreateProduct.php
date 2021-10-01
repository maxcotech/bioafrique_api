<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Models\ProductImage;
use App\Traits\FilePath;
use App\Traits\HasFile;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateProduct extends Action{
   use HasFile,FilePath;
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
         'product_name' => 'required|string',
         'product_sku' => 'required|string',
         'regular_price' => 'required|numeric',
         'sales_price' => 'nullable|numeric',
         'simple_description' => 'required|string',
         'description' => 'nullable|string',
         'amount_in_stock' => 'required|numeric',
         'category_id' => 'required|integer|exists:categories,id',
         'main_product_image'=>'required|file|mimes:jpg,jpeg,gif,png',
         'brand'=>'required|integer|exists:brands,id',
         'youtube_video_id'=>'nullable|string',
         'product_type' => 'required|integer',
      ]);
      $category = Category::where('id',$this->request->category_id)->first();
      foreach($this->img_labels as $label){
         $this->validateGalleryImage($val,$label,$category);
      }
      $val->sometimes('variations','required|json',function(){
         if($this->request->product_type != 2){
            return false;
         } else {
            return true;
         }
      });
      return $this->valResult($val);
   }

   protected function uploadGalleryImages(){
      $file_urls = [];
      $request_array = $this->request->all();
      foreach($this->img_labels as $label){
         $file = $request_array[$label] ?? null;
         if(isset($file)){
            $url = $this->uploadImage($file,'product_gallery');
            if(isset($url)){
               array_push($file_urls,[
                  'image_type'=>$label,
                  'image_url'=>$url
               ]);
            }
         }
      }
      return $file_urls;
   }

   protected function saveGalleryImages(array $image_list,int $product_id,$store_id){
      foreach($image_list as $image_obj){
         ProductImage::create([
            ''
         ]);
      }
   }

   protected function validateVariations(){
      $variations = json_decode($this->request->variations,true);
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
      $val->sometimes($param_label,'required|file|mimes:jpg,jpeg,png,gif,webp',function($input) use($param_label,$cat){
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
         if($this->request->product_type == 2){
            $val2 = $this->validateVariations();
            if($val2['status'] != "success") return $this->resp($val2);
         }

      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   