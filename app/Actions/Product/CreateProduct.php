<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;

class CreateProduct extends Action{
    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
    }
    protected function validate(){
       $val = validator::make($this->request->all(),[
           'product_name' => 'required|string',
           'product_sku' => 'string',
           'product_slug' => 'required|string|unique:products,product_slug',
           'regular_price' => 'required|numeric',
           'sales_price' => 'numeric',
           'sales_price_expiry' => 'date|after:today',
           'stock_threshold' => 'integer',
           'simple_description' => 'required|string',
           'description' => 'string',
           'product_tags'=>'required|json',
           'product_gallery'=>'required|json',
           'main_product_image'=>'required|file|mimes:jpg,jpeg,gif,png',
           'brand'=>'integer|exists:brands,id',
           'video_urls'=>'json',
           'product_type'=>

       ]);
       $val->sometimes('amount_in_stock','required|integer',function($input){
           if($this->request->availability == 3 || $this->request->availability == 2){
              return true;
           }else{
              return false;
           }
       });

       return $this->valResult($val);
    }
    public function execute(){
       try{
          $val = $this->validate();
          if($val['status'] != "success") return $this->resp($val);
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    