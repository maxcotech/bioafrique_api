<?php
namespace App\Actions\Widget;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Widget;
use App\Models\WidgetItem;
use App\Traits\FilePath;
use App\Traits\HasArrayOperations;
use App\Traits\HasFile;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UploadWidgetItems extends Action{
   use HasFile,HasArrayOperations,HasRoles,HasResourceStatus,FilePath;
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'required|integer|exists:widgets,id',
         'widget_items' => 'required|json'
      ]);
      return $this->valResult($val);
   }

   protected function validateItems($items){
      if(count($items) > 0){
         foreach($items as $item){
            $val = Validator::make($item,[
               'id' => ['nullable','integer',Rule::exists('widget_items','id')
               ->where('widget_id',$this->request->id)],
               'item_title' => 'nullable|string|max:255',
               'item_link' => 'nullable|string|max:255',
               'item_image_url' => 'required|string'
            ]);
            if($val->fails()){
               return $this->valResult($val);
            } else {
               return $this->payload();
            }
         }
      } else {
         return $this->valMessageObject("At least one widget item is required.");
      }
   }

   protected function saveItems($items){
      foreach($items as $item){
         if(isset($item['id'])){
            WidgetItem::where('id',$item['id'])
            ->where('widget_id',$this->request->id)
            ->update([
               'item_title' => $item['item_title'] ?? null,
               'item_link' => $item['item_link'] ?? null
            ]);
         } else {
            WidgetItem::create([
               'widget_id' => $this->request->id,
               'item_title' => $item['item_title'] ?? null,
               'item_link' => $item['item_link'] ?? null
            ]);
         }
      }
   }

   protected function deleteExcludedItems($items){
      $item_ids = [];
      if(count($items) > 0){
         foreach($items as $item){
            if(isset($item['id']) && !in_array($item['id'],$item_ids)){
               array_push($item_ids,$item['id']);
            }
         }
      }
      $excluded_items = WidgetItem::where('widget_id',$this->request->id)
      ->whereNotIn('id',$item_ids)->get();
      if(count($excluded_items) > 0){
         foreach($excluded_items as $item){
            $this->deleteFile($this->getInitialPath($item->item_image_url,UploadWidgetImage::upload_path));
         }
         WidgetItem::where('widget_id',$this->request->id)
         ->whereNotIn('id',$item_ids)->delete();
      }
   }

   protected function updateWidgetStatus(){
      $widget = Widget::find($this->request->id);
      if($this->isSuperAdmin($this->user->user_type)){
         if($widget->status == $this->getResourceInDraftId()){
            $widget->status = $this->getResourceActiveId();
            $widget->save();
         }
      }
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $items = json_decode($this->request->widget_items,true);
         $val2 = $this->validateItems($items);
         if($val2['status'] != "success") return $this->resp($val2);
         DB::transaction(function()use($items){
            $this->deleteExcludedItems($items);
            $this->saveItems($items);
            $this->updateWidgetStatus();         
         });
         return $this->successMessage('Widget items uploaded successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   