<?php
namespace App\Actions\Country;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Country;
use App\Traits\FilePath;
use App\Traits\HasFile;

class UploadLogo extends Action{
   use HasFile,FilePath;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'country_id' => 'required|integer|exists:countries,id',
         'country_logo' => 'required|file|mimes:jpg,jpeg,gif,png,webp'
      ]);
      return $this->valResult($val);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $country = Country::find($this->request->country_id);
         if($country->country_logo != null){
            $this->deleteFile($this->getInitialPath($country->country_logo,CreateCountry::uploadPath));
         }
         $url = $this->uploadImage(
            $this->request->country_logo,
            CreateCountry::uploadPath
         );
         Country::where('id',$this->request->country_id)->update([
            'country_logo' => $url
         ]);
         return $this->successWithData($this->getRealPath($url),'Country logo uploaded successfully');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   