<?php
namespace App\Actions\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;
use App\Traits\FilePath;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateStoreLogo extends Action{
    use FilePath;
    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
       $this->user=Auth::user();
    }
    protected function validate(){
       $val = Validator::make($this->request->all(),[
          'store_logo'=>'required|file|mimes:jpg,jpeg,png,gif,webp'
       ]);
       return $this->resp($val);
    }
    
    protected function deletePreviousLogo($store){
      if($store->store_logo != null){
         Storage::disk(env('CURRENT_DISK'))->delete(
            $this->getInitialPath($store->store_logo,'stores'));
      }
    }
    
    public function execute(){
       try{
          //
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    