<?php

namespace App\Actions\Category;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Traits\FilePath;
use Illuminate\Support\Facades\Storage;

class CreateCategory extends Action
{
   use FilePath;
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
         'category_image' => 'required|file|mimes:jpeg,jpg,gif,webp,png',
         'parent_id' => 'sometimes|integer|exists:categories,id',
         'display_title' => 'string|unique:categories,display_title'
      ]);
      return $this->valResult($val);
   }
   protected function uploadImage()
   {
      $file_url = Storage::disk(env('CURRENT_DISK'))->put(
         'categories',
         $this->request->category_image
      );
      return $file_url;
   }
   protected function createCategory($file_url){
      Category::create([
         'category_title'=>$this->request->category_title,
         'category_slug'=>$this->request->category_slug,
         'category_image'=>$file_url,
         'parent_id'=>$this->request->input('parent_id',0),
         'category_level'=>$this->getNewCategoryLevel(),
         'display_title'=>$this->request->display_title
      ]);
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
        $file_url = $this->uploadImage();
        $this->createCategory($file_url);
        return $this->successMessage('Category successfully created.');
      } catch (\Exception $e) {
         return $this->internalError($e->getMessage());
      }
   }
   
}
