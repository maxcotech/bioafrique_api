<?php
namespace App\Actions\Country;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Country;
use App\Traits\HasFile;
use App\Traits\HasRoles;

class CreateCountry extends Action{
   use HasFile,HasRoles;
   public const uploadPath = "countries";
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'country_code' => 'required|string|unique:countries,country_code',
         'country_name' => 'required|string|unique:countries,country_name',
         'country_tel_code' => 'required|string',
         'country_logo' => 'required|file|mimes:jpg,jpeg,png,gif,webp'
      ]);
      return $this->valResult($val);
   }

   protected function onCreateCountry(){
      $data = $this->request->all(['country_code','country_name','country_tel_code']);
      $data['country_logo'] = $this->uploadImage(
         $this->request->country_logo,self::uploadPath);
      Country::create($data);
   }
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if($this->isSuperAdmin()){
            $this->onCreateCountry();
            return $this->successMessage('Country account created successfully');
         } else {
            return $this->notAuthorized('You are not authorized to carry out this operation.');
         }
         
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   