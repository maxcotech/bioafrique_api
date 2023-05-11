<?php
namespace App\Actions\Widget;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Widget;
use App\Models\WidgetItem;
use App\Traits\FilePath;
use App\Traits\HasFile;
use Illuminate\Support\Facades\DB;

class DeleteWidget extends Action{
   use HasFile,FilePath;
   protected $request;
   protected $widget_id;
   public function __construct(Request $request,$widget_id){
      $this->request=$request;
      $this->widget_id = $widget_id;
   }

   protected function deleteAttributes(){
      $items = WidgetItem::where('widget_id',$this->widget_id)->get();
      if(count($items) > 0){
         foreach($items as $item){
            $this->deleteFile($this->getInitialPath($item->item_image_url,UploadWidgetImage::upload_path));
            $item->delete();
         }
      }
   }

   public function execute(){
      try{
         DB::transaction(function(){
            $this->deleteAttributes();
            Widget::where('id',$this->widget_id)->delete();
         });
         return $this->successMessage('Widget Deleted Successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   