<?php
namespace App\Actions\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;

class SearchStore extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'query' => 'required|string'
      ]);
      return $this->valResult($val);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != 'success') return $this->successWithData([]);
         $query = $this->request->query('query',null);
         $stores =  Store::where('store_name','like',"%$query%")
         ->limit(15)->select('store_name','store_slug','store_logo','id')->get();
         return $this->successWithData($stores);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   