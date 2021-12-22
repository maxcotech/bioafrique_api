<?php
namespace App\Actions\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\User;

class GetUsers extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'account_status' => 'nullable|integer',
         'user_type' => 'nullable|integer',
         'limit' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getUsers(){
      $query = User::orderBy('id','desc');
      if($this->request->query('account_status',null) != null){
         $query = $query->where('account_status',$this->request->query('account_status'));
      }
      if($this->request->query('user_type',null) != null){
         $query = $query->where('user_type',$this->request->query('user_type'));
      }
      $result = $query->paginate($this->request->query('limit',15));
      $result->each(function($item){
         $item->append(['account_status_text','user_type_text']);
      });
      return $result;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $users = $this->getUsers();
         return $this->successWithData($users);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   