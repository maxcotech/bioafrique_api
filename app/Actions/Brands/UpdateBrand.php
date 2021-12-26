<?php
namespace App\Actions\Brands;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;
use App\Traits\HasBrand;
use Illuminate\Validation\Rule;

class UpdateBrand extends Action{
   use HasBrand;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'required|integer|exists:brands,id',
         'brand_name' => ['required','string', 
          Rule::unique('brands','brand_name')->where(function($query){
             return $query->where('id','!=',$this->request->id);
          })],
         'website_url' => 'nullable|string',
      ]);
      return $this->valResult($val);
   }

   protected function updateBrand(){
      $data = [
         'brand_name' => $this->request->brand_name,
         'website_url' => $this->request->website_url,
         'status' => $this->getBrandDefaultStatus()
      ];
      Brand::where('id',$this->request->id)->update($data);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != 'success') return $this->resp($val);
         if($this->isSuperAdmin()){
            $this->updateBrand();
            return $this->successMessage('Brand Successfully updated');         
         } else {
            return $this->notAuthorized('You are not authorized to carry out this operation');
         }
         
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   