<?php

namespace App\Actions\Category;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use App\Traits\HasAuthStatus;
use App\Traits\HasCategory;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;

class GetCategories extends Action
{
   use HasCategory,HasRoles,HasResourceStatus,HasAuthStatus;
   protected $request;
   protected $max_cat_level;
   protected $min_cat_level;
   protected $user;

   public function __construct(Request $request)
   {
      $this->request = $request;
      $this->max_cat_level = 10;
      $this->min_cat_level = 1;
      $this->user = $request->user();
   }
   protected function validate()
   {
      $val = Validator::make($this->request->all(), [
         'parent' => 'nullable|integer|exists:categories,id',
         'parent_slug' => 'nullable|string|exists:categories,category_slug',
         'levels' => 'nullable|integer|min:0,max:100',
         'limit' => 'nullable|integer|min:1',
         'verbose' => 'nullable|integer',
         'child_verbose' => 'nullable|integer',
         'status' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function selectByCategoryStatus($query){
      if($this->user != null){
         if(!$this->isSuperAdmin($this->user->user_type)){
            $query = $query->where('status',$this->getResourceActiveId());
         } else {
            if($this->request->query('status',null) != null){
               $query = $query->where('status',$this->request->query('status'));
            }
         }
      } else {
         $query = $query->where('status',$this->getResourceActiveId());
      }
      return $query;
   }

   protected function getCategories()
   {
      $query = null;
      if($this->request->query('parent',null) != null){
         $query = Category::where('parent_id',$this->request->query('parent'));
      } elseif ($this->request->query('parent_slug',null) != null){
         $category = Category::where('category_slug',$this->request->query('parent_slug'))->first();
         $query = Category::where('parent_id',$category->id);
      } else {
         $query = Category::where('category_level',Category::MAIN_CATEGORY_LEVEL);
      }
      $query = $this->selectByCategoryStatus($query);
      $query = $this->selectByVerboseLevel($query,$this->request->query('verbose',1));
      return $query->paginate($this->request->query('limit',15));
   }


   protected function generateSubCategories($data, $init_level = 1, $max_level = 100)
   {
      $new_data = [];
      if (empty($data) || $init_level == $max_level) return $data;
      $child_verbose = $this->request->query('child_verbose',$this->request->query('verbose',1));
      foreach($data as $category){
         $query = Category::where('parent_id',$category['id']);
         $query = $this->selectByCategoryStatus($query);
         $query = $this->selectByVerboseLevel($query,$child_verbose);
         $sub_cats = $query->get();
         $sub_cats = json_decode(json_encode($sub_cats),true);
         $category['sub_categories'] = $this->generateSubCategories($sub_cats,$init_level + 1,$max_level);
         array_push($new_data,$category);
      }
      return $new_data;
   }



   public function execute()
   {
      try {
         $val = $this->validate();
         if ($val['status'] != "success") return $this->resp($val);
         $data = $this->getCategories();
         $data_json = $data->toJson();
         $new_data = json_decode($data_json, true);
         $new_data['data'] = $this->generateSubCategories(
            $new_data['data'],
            $this->min_cat_level,
            $this->request->query('levels',$this->min_cat_level)
         );
         return $this->successWithData($new_data);
      } catch (\Exception $e) {
         return $this->internalError($e->getMessage());
      }
   }
}
