<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Traits\HasAuthStatus;
use App\Traits\HasCategory;
use App\Traits\HasProductFilters;
use App\Traits\HasRoles;

class GetProducts extends Action{
   use HasProductFilters,HasCategory,HasAuthStatus,HasRoles;

   protected $request;
   protected $default_page_count = 30;
   protected $user,$access_type;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
      $this->access_type = $this->getUserAuthTypeObject($this->user);
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'max_price' => 'nullable|numeric|min:0',
         'min_price' => 'nullable|numeric|min:0',
         'brand' => 'nullable|integer|exists:brands,id',
         'store' => $this->getStoreValidationRule($this->access_type,$this->user),
         'limit' => 'nullable|integer',
         'country' => 'nullable|integer|exists:countries,id',
         'state' => 'nullable|integer|exists:states,id',
         'city' => 'nullable|integer|exists:cities,id',
         'query' => 'nullable|string',
         'rating' => 'nullable|integer',
         'status' => 'nullable|integer'
      ]); 
      return $this->valResult($val);
   }

   protected function getProductsQuery(){
      $query = Product::select(
         'id','product_name','product_image','regular_price','sales_price','product_slug','store_id','product_type',
         'product_status','amount_in_stock');
      $query = $query->orderBy('id','desc');
      $query = $this->filterByProductStatus($query,$this->access_type,$this->user);
      $query = $this->filterByRating($query);
      $query = $this->filterBySearchQuery($query);
      $query = $this->filterByBrandAndStore($query);
      $query = $this->filterByPrice($query);
      $query = $this->filterByLocation($query);
      $query = $this->selectFields($query);
      return $query;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $query = $this->getProductsQuery();
         $data = $query->paginate($this->request->query('limit',$this->default_page_count));
         $data = collect(['filters'=>$this->getProductFilterArray(null,$this->request->query('query',null))])->merge($data);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   