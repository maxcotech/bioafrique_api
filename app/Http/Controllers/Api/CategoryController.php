<?php

namespace App\Http\Controllers\Api;

use App\Actions\Category\CreateCategory;
use App\Actions\Category\DeleteCategory;
use App\Actions\Category\GetCategories;
use App\Actions\Category\UpdateCategory;
use App\Actions\Category\UpdateCategoryImage;
use App\Actions\Category\UpdateCategoryStatus;
use App\Actions\Category\UploadCategoryIcon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create(Request $request){
        return (new CreateCategory($request))->execute();
    }
    public function index(Request $request){
        return (new GetCategories($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateCategory($request))->execute();
    }
    public function delete(Request $request,$category_id){
        return (new DeleteCategory($request,$category_id))->execute();
    }
    public function updateCategoryImage(Request $request){
        return (new UpdateCategoryImage($request))->execute();
    }
    public function uploadCategoryIcon(Request $request){
        return (new UploadCategoryIcon($request))->execute();
    }
    public function updateCategoryStatus(Request $request){
        return (new UpdateCategoryStatus($request))->execute();
    }
    
}
