<?php
namespace App\Actions\Widget;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Widget;
use Illuminate\Support\Facades\DB;

class SwapWidgetIndex extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'required|integer|exists:widgets,id',
         'index' => 'required|integer|exists:widgets,index_no'
      ]);
      return $this->valResult($val);
   }

   protected function onSwapIndex(){
      $req_index = $this->request->index;
      $target_widget = Widget::find($this->request->id);
      $swap_widget = Widget::where('index_no',$req_index)->first();
      if(isset($target_widget) && isset($swap_widget)){
         DB::transaction(function()use($req_index,$target_widget,$swap_widget){
            $old_index = $target_widget->index_no;
            $target_widget->update(['index_no' => 0]);
            $swap_widget->update(['index_no' => $old_index]);
            $target_widget->update(['index_no' => $req_index]);
         });
      }
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $this->onSwapIndex();
         return $this->successMessage('Widget indexes swapped successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   