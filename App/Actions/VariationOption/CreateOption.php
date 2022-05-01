<?php
namespace App\Actions\VariationOption;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\VariationOption;

class CreateOption extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'option' => "required|string|unique:variation_options,option",
         'option_data_type' => 'nullable|string'
      ]);
      return $this->valResult($val);
   }

   protected function createOption(){
      VariationOption::create([
         'option' => $this->request->option,
         'option_data_type' => $this->request->option_data_type
      ]);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $this->createOption();
         return $this->successMessage('Variation Option created successfully');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   