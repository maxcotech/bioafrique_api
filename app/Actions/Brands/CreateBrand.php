<?php
namespace App\Actions\Brands;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;
use App\Traits\HasFile;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;

class CreateBrand extends Action{
   use HasFile,HasRoles,HasResourceStatus;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'brand_name' => 'required|string|unique:brands,brand_name',
         'website_url' => 'nullable|string',
         'brand_logo' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp'
      ]);
      return $this->valResult($val);
   }

   protected function getBrandDefaultStatus(){
      if($this->isStoreOwner() || $this->isStoreStaff()){
         return $this->getResourceInactiveId();
      } else if($this->isSuperAdmin()){
         return $this->getResourceActiveId();
      } else {
         throw new \Exception('Could not determine the category of your profile.');
      }
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $file_url = $this->uploadImage($this->request->brand_logo,'brands');
         Brand::create([
            'brand_name' => $this->request->brand_name,
            'brand_logo' => $file_url,
            'website_url' => $this->request->website_url,
            'status' => $this->getBrandDefaultStatus()
         ]);
         return $this->successMessage('Brand created successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   