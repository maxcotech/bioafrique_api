<?php
namespace App\Actions\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;

class GetCategories extends Action{
    protected $request;
    protected $max_cat_level;
    protected $min_cat_level;
    public function __construct(Request $request){
       $this->request=$request;
       $this->max_cat_level = 10;
       $this->min_cat_level = 1;
    }
    protected function validate(){
       $val = Validator::make($this->request->all(),[
         'category_level'=>'integer',
         'verbose'=>'integer',
         'limit'=>'integer',
         'sub_cat_limit' => 'integer',
         'max_level'=>'integer',
         'min_level'=>'integer',
         'parent'=>'integer|exists:categories,id',
         'parent_slug'=>'string|exists:categories,categories,category_slug'
       ]);
       return $this->valResult($val);
    }
    protected function getCategories(){
      $query = Category::where('category_level',$this->request->query('min_level',$this->min_cat_level));
      if($this->request->filled('parent')){
         $query->where('parent_id',$this->request->query('parent'));
      }
      if($this->request->filled('category_slug')){
         $query->where('category_slug',$this->request->query('parent_slug'));
      }
      $this->selectByVerboseLevel($query);
      $data = $query->paginate($this->request->query('limit',15));
      return $data;
    }

    protected function selectByVerboseLevel(&$query){
      if($this->request->filled('verbose')){
        switch($this->request->query('verbose')){
           case 1:$query->select('id','category_title','category_level','parent_id');break;
           case 2:$query->select('id','category_title','category_level','parent_id','display_title');break;
           case 3:$query->select('id','category_title','category_level','parent_id','display_title','image_thumbnail');break;
           case 4:$query->select('id','category_title','category_level','parent_id','display_title','image_thumbnail','category_image');break;
           default:;
        }
      }
   }

   protected function generateSubCategories($data,$level = 1, $parent_id = 0){
      $new_data = [];
      if(empty($data)) return [];
      foreach($data as $cat){
         if($cat['category_level'] == $level && $cat['parent_id'] == $parent_id){
            $next_level = $level + 1;
            if($this->request->filled('max_level')){
               if($next_level > $this->request->query('max_level',$this->max_cat_level)){
                  break;
               }
            }
            $query = Category::where('category_level',$next_level)
            ->where('parent_id',$cat['id'])->limit($this->request->query('sub_cat_limit',1000));
            $this->selectByVerboseLevel($query);
            $sub_cats = $query->get();
            $cat['sub_categories'] = $this->generateSubCategories($sub_cats,$next_level,$cat['id']);
            array_push($new_data,$cat);
         }
      }
      return $new_data;
   }
    
    

    public function execute(){
      try{
          $val = $this->validate();
          if($val['status'] != "success") return $this->resp($val);
          $data = $this->getCategories();
          $data_json = $data->toJson();
          $new_data = json_decode($data_json,true);
          $new_data['data'] = $this->generateSubCategories($new_data['data']);
          return $this->successWithData($new_data);
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }
    

}
    