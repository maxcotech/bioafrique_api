<?php
namespace App\Actions\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Traits\HasFile;

class UploadCategoryIcon extends Action{
    use HasFile;
    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
    }

    protected function validate(){
      $val = Validator::make($this->request->all(),[
         'category_id' => 'required|integer|exists:categories,id',
         'category_icon' => 'required|file|mimes:jpg,jpeg,png,webp,gif'
      ]);
      return $this->valResult($val);
    }


    public function execute(){
       try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $file_url = $this->uploadImage($this->request->category_icon,'categories');
         Category::where('id',$this->request->category_id)
         ->update(['category_icon' => $file_url]);
         return $this->successMessage('Category icon successfully loaded.');
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    