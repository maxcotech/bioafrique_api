<?php
namespace App\Actions\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Traits\HasRoles;
use App\Traits\StringFormatter;
use Illuminate\Validation\Rule;

class UpdateCategory extends Action{
   use StringFormatter,HasRoles;

   protected $request,$user;

   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'required|integer|exists:categories,id',
         'category_title' => ['required','string',Rule::unique('categories','category_title')
         ->where(function($query){
            $query->where('id','!=',$this->request->id);
         })],
         'display_level' => 'nullable|integer',
         'display_title' => 'nullable|string',
         'commission_fee' => $this->getCommissionFeeRules()
      ]);
      return $this->valResult($val);
   }

   protected function getCommissionFeeRules(){
      if($this->isSuperAdmin($this->user->user_type)){
         return 'required|numeric';
      } else {
         return "nullable|numeric";
      }
   }

   protected function generateCategorySlug(){
      $cat_id = $this->request->id;
      $cat_title = $this->request->category_title;
      $slug = $this->generateSlugFromString($cat_title);
      if(Category::where('category_slug',$slug)->where('id','!=',$cat_id)->exists()){
         throw new \Exception("A category bearing the category title you are trying to use in this update already exists.");
      }
      return $slug;
   }

   protected function updateCategory($cat_slug){
      $update_data = [
      'category_title'=>$this->request->category_title,
      'category_slug'=>$cat_slug,
      ];
      if($this->request->filled('display_level')){
         $update_data['display_level'] = $this->request->display_level;
      }
      if($this->isSuperAdmin($this->user->user_type)){
         $update_data['commission_fee'] = $this->request->commission_fee;
      }
      Category::where('id',$this->request->id)->update($update_data);

   }
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != 'success') return $this->resp($val);
         $new_slug = $this->generateCategorySlug();
         $this->updateCategory($new_slug);
         return $this->successMessage('Category Successfully Updated.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   