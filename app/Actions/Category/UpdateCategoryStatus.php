<?php
namespace App\Actions\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Traits\HasRoles;

class UpdateCategoryStatus extends Action{
   use HasRoles;
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request = $request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'required|integer|exists:categories,id',
         'status' => 'required|integer'
      ]);
      return $this->valResult($val);
   }

   protected function updateStatus(){
      Category::where('id',$this->request->id)->update([
         'status' => $this->request->status
      ]);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if($this->isSuperAdmin($this->user->user_type)){
            $this->updateStatus();
            return $this->successMessage('Category status updated successfully');
         } else {
            return $this->notAuthorized('You are not authorized to carry out this operation.');
         }
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   