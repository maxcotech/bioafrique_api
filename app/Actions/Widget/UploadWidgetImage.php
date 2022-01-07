<?php
namespace App\Actions\Widget;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\WidgetItem;
use App\Traits\FilePath;
use App\Traits\HasFile;
use Illuminate\Validation\Rule;

class UploadWidgetImage extends Action{
   use HasFile,FilePath;
   protected $request;
   public const upload_path = "widgets";
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'image_file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,JPEG,JPG',
         'widget_id' => 'required|integer|exists:widgets,id',
         'id' => ['nullable','integer',Rule::exists('widget_items','id')
         ->where('widget_id',$this->request->widget_id)]
      ]);
      return $this->valResult($val);
   }

   protected function uploadFileRecord($path){
      $item_id = $this->request->input('id',null);
      $widget_id = $this->request->widget_id;
      if(isset($path)){
         if(isset($item_id)){
            $widget_item = WidgetItem::where('widget_id',$widget_id)
            ->where('id',$item_id)->first();
            $this->deleteFile($this->getInitialPath($widget_item->item_image_url,self::upload_path));
            $widget_item->update(['item_image_url' => $path]);
            $widget_item->refresh();
            return $widget_item;
         } else {
            return WidgetItem::create([
               'widget_id' => $widget_id,'item_image_url' => $path
            ]);
         }
      }
   }

   protected function publishWidget(){
      
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $image_file = $this->request->image_file;
         $path = self::upload_path;
         $file_path = $this->uploadImage($image_file,$path);
         $record = $this->uploadFileRecord($file_path);
         return $this->successWithData($record,'Widget item image uploaded successfully');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   