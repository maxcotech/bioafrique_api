<?php
namespace App\Actions\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Traits\FilePath;
use Illuminate\Support\Facades\Storage;

class UpdateCategoryImage extends Action{
    use FilePath;

    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
    }
    protected function validate(){
       $val = Validator::make($this->request->all(),[
          'category_image' => 'required|file|mimes:jpg,png,gif,jpeg',
          'category_id' => 'required|exists:categories,id'
       ]);
       return $this->valResult($val);
    }
    protected function deletePreviousImages($cat){
       Storage::disk(env('CURRENT_DISK'))->delete($this->getInitialPath(
          $cat->category_image,'categories')
       );
       if($cat->image_thumbnail != null){
          Storage::disk(env('CURRENT_DISK'))->delete($this->getInitialPath(
             $cat->image_thumbnail,$this->getThumbnailPath()
          ));
       }
    }
    
    protected function uploadImage(){
       $file_url = $this->request->file('category_image')
       ->store('categories',env('CURRENT_DISK'));
       return $file_url;
    }
    public function execute(){
       try{
          $val = $this->validate();
          if($val['status'] != "success") return $this->resp($val);
          $category = Category::find($this->request->category_id);
          $this->deletePreviousImages($category);
          $file_url = $this->uploadImage();
          $thumb_url = $this->createAndUploadThumbnail($this->request->file('category_image'));
          $category->update([
            'category_image' => $file_url,
            'image_thumbnail' => $thumb_url
          ]);
          return $this->successMessage('Successfully uploaded category image');
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    