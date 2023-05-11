<?php
namespace App\Actions\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;

class UpdateStoreStatus extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'status' => 'required|integer',
         'id' => 'required|integer|exists:stores,id'
      ]);
      return $this->valResult($val);
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         Store::where('id',$this->request->id)->update([
            'store_status' => $this->request->status
         ]);
         return $this->successMessage('Store status was updated successfully');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   