<?php
namespace App\Actions\Widget;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Widget;

class UpdateWidgetStatus extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'required|integer|exists:widgets,id',
         'status' => 'required|integer'
      ]);
      return $this->valResult($val);
   }

   protected function onUpdateStatus(){
      Widget::where('id',$this->request->id)->update([
         'status' => $this->request->status
      ]);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $this->onUpdateStatus();
         return $this->successMessage('Widget Status Updated successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   