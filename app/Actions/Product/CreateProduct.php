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
       ]);
       $val->sometimes('front_image','required|integer',function($input){
           
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
    