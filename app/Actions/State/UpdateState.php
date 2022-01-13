<?php
namespace App\Actions\State;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\State;
use Illuminate\Validation\Rule;

class UpdateState extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'required|integer|exists:states,id',
         'country_id' => 'required|integer|exists:countries,id',
         'state_name' => ['required','string',Rule::unique('states','state_name')->where(function($query){
            $query->where('country_id',$this->request->country_id)
            ->where('id',"!=",$this->request->id);
         })],
         'state_code' => ['nullable','string',Rule::unique('states','state_code')->where(function($query){
            $query->where('country_id',$this->request->country_id)
            ->where('id',"!=",$this->request->id);
         })]
      ]);
      return $this->valResult($val);
   }

   protected function onUpdate(){
      State::where('id',$this->request->id)->update(
         $this->request->all(['state_name','country_id','state_code'])
      );
   }
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $this->onUpdate();
         return $this->successMessage('State updated successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   