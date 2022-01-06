<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Models\User;
use App\Traits\HasAuthStatus;
use App\Traits\HasCategory;
use App\Traits\HasProductFilters;
use App\Traits\HasRoles;

class GetProducts extends Action{
   use HasProductFilters,HasCategory,HasAuthStatus,HasRoles;

   protected $request;
   protected $default_page_count = 30;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
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
         'status' => 'nullable|integer'
      ]); 
      return $this->valResult($val);
   }

   protected function filterByProductStatus($query){
      $user = $this->request->user();
      $auth_type = $this->getUserAuthTypeObject($user);
      if(!isset($auth_type)) throw new \Exception(json_encode($this->request->cookies));
      throw new \Exception(json_encode($auth_type));
      if($auth_type->type == User::auth_type && isset($user)){
         $user_type = $user->user_type;
         if($this->isStoreOwner($user_type) || $this->isStoreStaff($user_type) || $this->isSuperAdmin($user_type)){
            $status = $this->request->query('status',null);
            if($status != null){
               $query = $query->where('product_status',$status);
            }
         } else {
            $query = $query->where('product_status',$this->getResourceActiveId());
         }
      } else {
         $query = $query->where('product_status',$this->getResourceActiveId());
      }
      return $query;

   }

   
   protected function getProductsQuery(){
      $query = Product::select(
         'id','product_name','product_image','regular_price','sales_price','product_slug','store_id','product_type',
         'product_status','amount_in_stock');
      $query = $this->filterByProductStatus($query);
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
   