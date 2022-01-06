<?php
namespace App\Actions\Brands;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;

class GetBrands extends Action{
   use HasResourceStatus,HasRoles;

   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function hasSearchQuery(){
      $query = $this->request->query('query',null);
      if($query != null && trim($query) != ""){
         return true;
      }
      return false;
   }

   protected function selectByBrandStatus($query){
      if($this->user != null){
         if(!$this->isSuperAdmin($this->user->user_type)){
            $query = $query->where('status',$this->getResourceActiveId());
         } else {
            $status_query = $this->request->query('status',null);
            if($status_query != null){
               $query = $query->where('status',$status_query);
            }
         }
      } else {
         $query = $query->where('status',$this->getResourceActiveId());
      }
      return $query;
   }

   protected function filterBySearchQuery($query){
      if($this->hasSearchQuery()){
         $search_query = $this->request->query('query');
         $query = $query->where('brand_name','LIKE',"%$search_query%");
      }
      return $query;
   }


   public function execute(){
      try{
         $query = Brand::orderBy('id','desc');
         $query = $this->selectByBrandStatus($query);
         $query = $this->filterBySearchQuery($query);
         $data = $query->paginate($this->request->query('limit',15));
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   