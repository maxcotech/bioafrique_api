<?php
namespace App\Actions\Brands;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;
use App\Traits\FilePath;
use App\Traits\HasFile;

class DeleteBrand extends Action{
   use HasFile,FilePath;
   protected $request;
   protected $brand_id;
   public function __construct(Request $request,$id){
      $this->request=$request;
      $this->brand_id = $id;
   }

   protected function validate(){
      $val = Validator::make(['brand_id'=>$this->brand_id],[
         'brand_id'=>'required|integer|exists:brands,id'
      ]);
      return $this->valResult($val);
   }

   protected function deleteRecordAndFile(){
      $brand = Brand::where('id',$this->brand_id)->first();
      if(!isset($brand)) return null;
      $brand_logo_url = $brand->brand_logo;
      $brand->delete();
      $this->deleteFile($this->getInitialPath($brand_logo_url,'brands'));
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $this->deleteRecordAndFile();
         return $this->successMessage("Successfully deleted brand.");
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   