<?php
namespace App\Actions\Widget;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\WidgetItem;

class GetWidgetItems extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'widget_id' => 'required|integer|exists:widgets,id'
      ]);
      return $this->valResult($val);
   }

   protected function onGetWidgetItems(){
      $widget_id = $this->request->widget_id;
      return WidgetItem::where('widget_id',$widget_id)->get();
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $items = $this->onGetWidgetItems();
         return $this->successWithData($items);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   