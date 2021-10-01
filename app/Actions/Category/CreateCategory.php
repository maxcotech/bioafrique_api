<?php

namespace App\Actions\Category;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Traits\FilePath;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreateCategory extends Action
{
   use FilePath,HasRoles,HasResourceStatus;
   protected $request;
   public function __construct(Request $request)
   {
      $this->request = $request;
   }
   protected function validate()
   {
      $val = Validator::make($this->request->all(), [
         'category_title' => 'required|unique:categories,category_title',
         'category_slug' => 'required|unique:categories,category_slug',
         'category_image' => 'nullable|file|mimes:jpeg,jpg,gif,webp,png',
         'category_icon' => 'nullable|file|mimes:jpeg,jpg,gif,webp,png',
         'parent_id' => 'nullable|integer|exists:categories,id',
         'display_title' => 'nullable|string|unique:categories,display_title',
         'display_level' => 'nullable|integer|max:4'
      ]);
      $val->sometimes('commission_fee','required|numeric',function(){
         if($this->isSuperAdmin()){
            return true;
         } else {
            return false;
         }
      });
      return $this->valResult($val);
   }

   protected function uploadImage($file)
   {
      $file_url = null;
      if(isset($file)){
         $file_url = Storage::disk(env('CURRENT_DISK'))->put(
            'categories',
            $file
         );
      }
      return $file_url;
   }

   protected function getNewCategoryStatus(){
      $user = Auth::user();
      $user_type = isset($user)? $user->user_type:null;
      if($this->isStoreManager($user_type) || $this->isStoreOwner($user_type)){
         return $this->getResourceInReviewId();
      } else if($this->isSuperAdmin($user_type)){
         return $this->getResourceActiveId();
      } else {
         throw new \Exception("Execution of this action is forbidden");
      }
   }


   protected function createCategory($cat_image,$cat_icon){
      $data = [
         'category_title'=>$this->request->category_title,
         'category_slug'=>$this->request->category_slug,
         'category_image'=>$cat_image,
         'category_icon'=>$cat_icon,
         'parent_id'=>$this->request->input('parent_id',0),
         'category_level'=>$this->getNewCategoryLevel(),
         'display_title'=>$this->request->display_title
      ];
      if($this->request->filled('display_level')){
         $data['display_level'] = $this->request->display_level;
      }
      if($this->isSuperAdmin() && $this->request->filled('commission_fee')){
         $data['commission_fee'] = $this->request->commission_fee;
      }
      $data['status'] = $this->getNewCategoryStatus();
      Category::create($data);
   }
   
   public function getNewCategoryLevel(){
      $parent_id = $this->request->input('parent_id');
      if(isset($parent_id)){
         $parent = Category::first($parent_id);
         if(isset($parent)){
            return $parent->category_level + 1;
         }
      }else{
         return 1;
      }
      
   }
   public function execute()
   {
      try {
        $val = $this->validate();
        if($val['status'] != 'success') return $this->resp($val);
        $cat_image = $this->uploadImage($this->request->category_image);
        $cat_icon = $this->uploadImage($this->request->category_icon);
        $this->createCategory($cat_image,$cat_icon);
        return $this->successMessage('Category successfully created.');
      } catch (\Exception $e) {
         return $this->internalError($e->getMessage());
      }
   }
   
}
