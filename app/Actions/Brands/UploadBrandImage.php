<?php
namespace App\Actions\Brands;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;
use App\Traits\HasFile;

class UploadBrandImage extends Action{
   use HasFile;

   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'brand_icon' => 'required|file|mimes:jpg,jpeg,png,gif,webp',
         'brand_id' => 'required|integer|exists:brands,id'
      ]);
      return $this->valResult($val);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $file_url = $this->uploadImage($this->request->brand_icon,'brands');
         Brand::where('id',$this->request->brand_id)
         ->update(['brand_logo' => $file_url]);
         return $this->successMessage('Brand Image Uploaded Successfully');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   