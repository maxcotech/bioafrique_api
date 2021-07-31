<?php
namespace App\Actions\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Category;
use Illuminate\Validation\Rule;

class UpdateCategory extends Action{
    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
    }

    protected function validate(){
       $val = Validator::make($this->request->all(),[
            'id' => 'required|integer|exists:categories,id',
            'category_title' => ['required','string',Rule::unique('categories','category_title')
            ->where(function($query){
               $query->where('id','!=',$this->request->id);
            })],
            'category_slug' => ['required','string',Rule::unique('categories','category_slug')
            ->where(function($query){
               $query->where('id','!=',$this->request->id);
            })]
       ]);
       return $this->valResult($val);
    }
    protected function updateCategory(){
       Category::where('id',$this->request->id)->update([
          'category_title'=>$this->request->category_title,
          'category_slug'=>$this->request->category_slug
       ]);

    }
    public function execute(){
       try{
          $val = $this->validate();
          if($val['status'] != 'success') return $this->resp($val);
          $this->updateCategory();
          return $this->successMessage('Category Successfully Updated.');
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    