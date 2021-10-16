<?php
namespace App\Actions\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;
use App\Traits\StringFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreateStore extends Action{
   use StringFormatter;

   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user=Auth::user();
   }

   protected function validate(){
      $val=Validator::make($this->request->all(),[
         'store_name' => 'required|string|unique:stores,store_name',
         'store_logo'=>'nullable|file|mimes:jpg,png,webp,jpeg',
         'store_address' => 'nullable|string',
         'store_email' => 'nullable|email',
         'store_telephone' => 'nullable|numeric',
         'country_id'=>'required|integer|exists:countries,id'
      ]);
      return $this->valResult($val);
   }
   ///^[a-z0-9]+ (?:-[a-z0-9]*)*$/ 
   protected function createStore($slug){
      Store::create([
         'store_name'=>$this->request->store_name,
         'store_slug'=>$slug,
         'store_logo'=>$this->uploadStoreLogo(),
         'store_address'=>$this->request->store_address,
         'store_email'=>$this->request->store_email,
         'store_telephone'=>$this->request->store_telephone,
         'country_id'=>$this->request->country_id,
         'user_id'=>$this->user->id
      ]);
   }
   protected function uploadStoreLogo(){
      $file_url = null;
      if($this->request->filled('store_logo')){
      $file_url = Storage::disk(env('CURRENT_DISK'))->put(
         'stores',
         $this->request->store_logo
      );
      }
      return $file_url;
   }

   protected function generateStoreSlug(){
      $store_name = $this->request->store_name;
      $slug = $this->generateSlugFromString($store_name);
      if(Store::where('store_slug',$slug)->exists()){
         throw new \Exception("A store bearing the name you wish to use here already exists.");
      }
      return $slug;

   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if(Store::where('user_id',$this->user->id)->exists()){
            return $this->validationError('A store instance already exists for your account.');
         }else{
            $slug = $this->generateStoreSlug();
            $this->createStore($slug);
            return $this->successMessage('Your store was successfully created');
         }
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   