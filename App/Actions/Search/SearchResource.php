<?php
namespace App\Actions\Search;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;
use App\Traits\HasResourceStatus;

class SearchResource extends Action{
   use HasResourceStatus;

   protected $request;
   protected $search_type;

   public function __construct(Request $request,$type){
      $this->request=$request;
      $this->search_type = $type;
   }

   protected function validate(){
      $req_arr = $this->request->all();
      $req_arr['search_type'] = $this->search_type;
      $val = Validator::make($req_arr,[
         'query' => 'nullable|string',
         'search_type' => 'required|string',
         'paginate' => 'nullable|numeric'
      ]);
      return $this->valResult($val);
   }

   protected function getBrandsQuery($query){
      $query = Brand::where('brand_name','like',"%$query%")
      ->where('status',$this->getResourceActiveId());
      return $query;
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $queryString = $this->request->query('query',null);
         if(!isset($queryString) || $queryString == "") return $this->successWithData([]);
         $dbQuery = null;
         $data = null;
         switch($this->search_type){
            case "brands": $dbQuery = $this->getBrandsQuery($queryString);break;
            default:;
         }
         if(isset($dbQuery)){
            if($this->request->query('pagination',null) == 1){
               $data = $dbQuery->paginate($this->request->query('limit',null) ?? 15);
            } else {
               $data = $dbQuery->get();
            }
         }
         return $data? $this->successWithData($data):$this->validationError('An Error Occurred');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   