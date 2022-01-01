<?php
namespace App\Actions\Widget;
use Illuminate\Support\Facades\Validator;
use App\Models\Widget as WidgetModel;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Traits\HasResourceStatus;

class UploadWidget extends Action{
   use HasResourceStatus;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'nullable|integer|exists:widgets,id',
         'widget_title' => 'required|string|max:1000',
         'widget_link_text' => 'nullable|string|max:1000',
         'widget_link_address' => $this->getWidgetLinkAddressRule(),
         'widget_type' => 'required|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getWidgetLinkAddressRule(){
      $common_rule = "|string|max:1000";
      if($this->request->input('widget_link_text',null) == null){
         return "nullable".$common_rule;
      } else {
         return "required".$common_rule;
      }
   }

   protected function getNextIndexNumber(){
      $largest_index_row = WidgetModel::select('id','index_no')
      ->orderBy('index_no','desc')->first();
      if(isset($largest_index_row)){
         return $largest_index_row->index_no + 1;
      } else {
         return 1;
      }
   }

   protected function onUploadWidget(){
      $widget_id = $this->request->input('id',null);
      $data = [
         'widget_title' => $this->request->widget_title,
         'widget_link_text' => $this->request->widget_link_text,
         'widget_link_address' => $this->request->widget_link_address,
         'widget_type' => $this->request->widget_type
      ];
      if(isset($widget_id)){
         WidgetModel::where('id',$widget_id)
         ->update($data);
         return WidgetModel::find($widget_id);
      } else {
         $data['status'] = $this->getResourceInDraftId();
         $data['index_no'] = $this->getNextIndexNumber();
         return WidgetModel::create($data);
      }
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $data = $this->onUploadWidget();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   