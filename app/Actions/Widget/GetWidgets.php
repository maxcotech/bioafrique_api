<?php
namespace App\Actions\Widget;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\User;
use App\Models\Widget;
use App\Traits\HasAuthStatus;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;

class GetWidgets extends Action{
   use HasAuthStatus,HasRoles,HasResourceStatus;
   protected $request;
   protected $access_type;
   public function __construct(Request $request){
      $this->request=$request;
      $this->access_type = $this->getUserAuthTypeObject($request->user());
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'with_items' => 'nullable|integer|min:0,max:1',
         'with_indexes' => 'nullable|integer|min:0,max:1',
         'limit' => 'nullable|integer',
         'status' => 'nullable|integer',
         'paginate' => 'nullable|integer|min:0,max:1'
      ]);
      return $this->valResult($val);
   }

   protected function onGetWidgets(){
      $paginate = $this->request->query('paginate',1);
      $limit = $this->request->query('limit',30);
      $selected = ['id','widget_title','widget_link_text','widget_link_address','widget_type','index_no','status','is_block'];
      $with_items = $this->request->query('with_items',1);
      $query = Widget::orderBy('index_no','asc');
      if($with_items == 1){
         $query = $query->with(['items:id,widget_id,item_title,item_image_url,item_link']);
      }
      $query = $this->filterByStatus($query);
      if($paginate == 1){
         return $query->paginate($limit,$selected);
      } else {
         return $query->get($selected);
      }
   }

   protected function filterByStatus($query){
      $user_type = $this->access_type->type;
      $status = $this->request->query('status',null);
      if($user_type == User::auth_type){
         if($this->isSuperAdmin()){
            if(isset($status)){
               $query = $query->where('status',$status);
            }
         } else {
            $query = $query->where('status',$this->getResourceActiveId());
         }
      } else {
         $query = $query->where('status',$this->getResourceActiveId());
      }
      return $query;
   }

   protected function getWidgetIndexes(){
      $query = Widget::orderBy('index_no','asc');
      $query = $this->filterByStatus($query);
      return $query->pluck('index_no');
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $widgets = $this->onGetWidgets();
         $widgets->append(['widget_type_text','is_block_text']);
         $should_have_indexes = $this->request->query('with_indexes',0);
         if($should_have_indexes == 1){
            $widgets = collect(['indexes' => $this->getWidgetIndexes()])->merge($widgets);
         }
         return $this->successWithData($widgets);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   