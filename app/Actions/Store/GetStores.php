<?php
namespace App\Actions\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;

class GetStores extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'status' => 'nullable|integer',
         'limit' => 'nullable|integer',
         'query' => 'nullable|string'
      ]);
      return $this->valResult($val);
   }

   protected function getStores(){
      $query = Store::orderBy('id','desc');
      $search = $this->request->query('query');
      $status = $this->request->query('status',null);
      $limit = $this->request->query('limit',15);
      if($search != null){
         $query = $query->where('store_name','LIKE',"%$search%");
      }
      if($status != null){
         $query = $query->where('store_status',$status);
      }
      $query = $this->appendRelationships($query);
      $data = $query->paginate($limit);
      $data->append(['store_status_text']);
      return $data;
   }

   protected function appendRelationships($query){
      $query = $query->with([
         'country:id,country_name',
         'state:id,state_name',
         'city:id,city_name',
      ]);
      return $query;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $stores = $this->getStores();
         return $this->successWithData($stores);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   