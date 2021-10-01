<?php
namespace App\Actions\Brands;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;
use Illuminate\Validation\Rule;

class UpdateBrand extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'brand_id' => 'required|integer|exists:brands,id',
         'brand_name' => ['required','string', 
         'status' => 'nullable|integer|max:5',
          Rule::unique('brands','brand_name')->where(function($query){
             return $query->where('id','!=',$this->request->brand_id);
          })]
      ]);
      return $this->valResult($val);
   }

   protected function updateBrand(){
      $data = ['brand_name' => $this->request->brand_name];
      if($this->request->has('status') && $this->request->filled('status')){
         $data['status'] = $this->request->status;
      }
      Brand::where('id',$this->request->brand_id)->update($data);
      
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != 'success') return $this->resp($val);
         $this->updateBrand();
         return $this->successMessage('Brand Successfully updated');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   