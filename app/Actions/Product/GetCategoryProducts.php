<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Models\Product;
use App\Traits\HasArrayOperations;
use App\Traits\HasCategory;
use App\Traits\HasProductFilters;
use App\Traits\HasResourceStatus;

class GetCategoryProducts extends Action{
   use HasResourceStatus,HasProductFilters,HasCategory,HasArrayOperations;
   protected $request;
   protected $category_param;
   public function __construct(Request $request,$category_param){
      $this->request=$request;
      $this->category_param = $category_param;
   }

   protected function validate(){
      $data = $this->request->all();
      $data['category_parameter'] = $this->category_param;
      $val = Validator::make($data,[
         'category_parameter' => $this->getCategoryParamRules(),
         'max_price' => 'nullable|numeric|min:0',
         'min_price' => 'nullable|numeric|min:0',
         'brand' => 'nullable|integer|exists:brands,id',
         'store' => 'nullable|integer|exists:stores,id',
         'limit' => 'nullable|integer',
         'country' => 'nullable|integer|exists:countries,id',
         'state' => 'nullable|integer|exists:states,id',
         'city' => 'nullable|integer|exists:cities,id',
         'query' => 'nullable|string',
         'rating' => 'nullable|integer',
      ]);
      return $this->valResult($val);
   }

   protected function getCategoryParamRules(){
      if(is_numeric($this->category_param)){
         return 'required|integer|exists:categories,id';
      }
      return 'required|string|exists:categories,category_slug';
   }

   protected function getProductQuery($category){
      $query = Product::where('product_status',$this->getResourceActiveId());
      $query = $query->select('id','product_name','product_image','regular_price','sales_price','product_slug','store_id','product_type',
      'product_status','amount_in_stock');
      $query = $this->filterByRating($query);
      $query = $this->filterBySearchQuery($query);
      $query = $this->filterByBrandAndStore($query);
      $query = $this->filterByPrice($query);
      $query = $this->filterByLocation($query);
      $query = $this->filterByCategory($query,$category);
      $query = $this->selectFields($query);
      return $query;
   }

   protected function getInputCategory(){
      if(is_numeric($this->category_param)){
         return Category::find($this->category_param);
      } else {
         return Category::where('category_slug',$this->category_param)->first();
      }
   }

   protected function filterByCategory($query,$category){
      if(isset($category)){
         $categories = $this->getAllCategoryDescendants($category);
         $cat_ids = $this->extractUniqueValueList($categories,'id');
         array_push($cat_ids,$category->id);
         $query = $query->whereIn('category_id',$cat_ids);
      }
      return $query;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $category = $this->getInputCategory();
         $query = $this->getProductQuery($category);
         $data = $query->paginate($this->request->query('limit',15));
         $data = collect(['filters'=>$this->getProductFilterArray(
            $category->id,$this->request->query('query',null)
         )])->merge($data);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   