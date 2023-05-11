<?php
namespace App\Actions\Brands;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;

class UpdateBrandStatus extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'status' => 'required|integer',
         'id' => 'required|integer|exists:brands,id'
      ]);
      return $this->valResult($val);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         Brand::where('id',$this->request->id)->update(['status'=>$this->request->status]);
         return $this->successMessage('Brand status updated successfully');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   