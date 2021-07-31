<?php
namespace App\Actions\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Models\Product;
use App\Traits\FilePath;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteCategory extends Action{
    use FilePath;
    protected $request;
    protected $category_id;
    public function __construct(Request $request,$category_id){
       $this->request=$request;
       $this->category_id=$category_id;
    }
    protected function validate(){
       $val=Validator::make(['category'=>$this->category_id],[
          'category'=>'required|integer|exists:categories,id'
       ]);
       return $this->valResult($val);
    }
    protected function deleteCategory(){
         DB::transaction(function(){
            Product::chunkById(100,function($products){
               $products->categories()->detach($this->category_id);
            });
            $cat = Category::find($this->category_id);
            $this->deleteCategoryImage($cat);
            $cat->delete();
         });
    }
    protected function deleteCategoryImage($cat){
       Storage::disk(env('CURRENT_DISK'))->delete($this->getInitialPath(
          $cat->category_image,'categories'));
    }
    public function execute(){
       try{
          $val = $this->validate();
          if($val['status'] != "success") return $this->resp($val);
          $this->deleteCategory();
          return $this->successMessage('Successfully deleted category');
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    