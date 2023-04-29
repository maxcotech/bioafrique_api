<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Traits\HasRateConversion;
use App\Traits\HasStore;

class GetStoreProducts extends Action{
   use HasStore,HasRateConversion;
   protected $request,$user;

   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'status' => 'nullable|integer',
         'query' => 'nullable|string',
         'brand_id' => 'nullable|integer|exists:brands,id',
         'category_id' => 'nullable|integer|categories,id',
         'max_price' => 'nullable|numeric',
         'min_price' => 'nullable|numeric'
      ]);
      return $this->valResult($val);
   }

   protected function getProductsQuery(){
      $query = Product::where('store_id',$this->request->query('store_id'));
      if($this->request->query('status',null) != null){
         $query->where('product_status',$this->request->query('status'));
      }
      if($this->request->query('query',null) != null){
         $squery = $this->request->query('query');
         $query->where('product_name','like',"%$squery%");
      }
      if($this->request->query('brand_id',null) != null){
         $query->where('brand_id',$this->request->query('brand_id'));
      }
      if($this->request->query('category_id',null) != null){
         $query->where('category_id',$this->request->query('category_id'));
      }
      if($this->request->query('max_price',null) != null && $this->request->query('min_price',null) != null){
         $max_price = $this->userToBaseCurrency($this->request->query('max_price'),$this->user);
         $min_price = $this->userToBaseCurrency($this->request->query('min_price'),$this->user);
         $query->where(function($iquery)use($max_price,$min_price){
            return $iquery->whereBetween('sales_price',[$max_price,$min_price])
            ->orWhereBetween('regular_price',[$max_price,$min_price]);
         });
      }
      return $query;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $selects = ['id','product_sku','product_name','regular_price','sales_price','product_image','product_slug','amount_in_stock','product_status'];
         $query = $this->getProductsQuery();
         $data = $query->orderBy('id','desc')->paginate(15,$selects);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   