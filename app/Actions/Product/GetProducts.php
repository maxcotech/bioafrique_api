<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Traits\HasRateConversion;
use Illuminate\Support\Facades\DB;

class GetProducts extends Action{
   use HasRateConversion;

   protected $request;
   protected $default_page_count = 30;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'max_price' => 'nullable|numeric|min:0',
         'min_price' => 'nullable|numeric|min:0',
         'category' => 'nullable|integer|exists:categories,id',
         'brand' => 'nullable|integer|exists:brands,id',
         'store' => 'nullable|integer|exists:stores,id'
      ]); 
      return $this->valResult($val);
   }

   protected function getProductsQuery(){
      $query = Product::select('id','product_name','regular_price','sales_price','product_slug');
      if($this->request->query('category') != null){
         $query->where('category_id',$this->request->query('category'));
      }
      $this->filterByBrandAndStore($query);
      $this->filterByPrice($query);
      $this->selectFields($query);
      return $query;
   }

   protected function selectFields($query){
      $withFields = ['variations'=>function($query){
         $query->select('regular_price','sales_price');
      }];
      //->with($withFields);
      return $query->with($withFields);
   }

   protected function filterByBrandAndStore($query){
      if($this->request->query('brand') !== null){
         $query->where('brand_id',$this->request->query('brand'));
      }
      if($this->request->query('store') !== null){
         $query->where('store_id',$this->request->query('store'));
      }
      return $query;
   }

   protected function filterByPrice($query){
      if($this->request->min_price != null && $this->request->max_price != null){
         $real_max = $this->userToBaseCurrency($this->request->max_price);
         $real_min = $this->userToBaseCurrency($this->request->min_price);
         $query->where(function($query)use($real_max,$real_min){
            $query->whereBetween('regular_price',[$real_min,$real_max])
            ->orWhereBetween('sales_price',[$real_max,$real_min]);
         });
      }
      return $query;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $query = $this->getProductsQuery();
         $data = $query->paginate($this->default_page_count);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   