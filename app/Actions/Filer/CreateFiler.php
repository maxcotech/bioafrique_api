<?php
namespace App\Actions\Filer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;

class CreateFiler extends Action{
    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
    }
    protected function validate(){
       $val = Validator::make($this->request->all(),[
            'filer'=>'required',
            'filer.*.image'=>'required|file|mimes:jpg,png,jpeg,gif,webp',
            'filer.*.alt'=>'required|string'
       ]);
       return $this->valResult($val);
    }
    public function execute(){
       try{
          $val = $this->validate();
          if($val['status'] != 'success') return $this->resp($val);
          /*foreach($this->request->filer as $item){
             $val = Validator::make(json_decode($item,true),[
                'image'=>'required|file|mimes:jpg,png,jpeg,gif,webp',
                'alt'=>'required|string'
             ]);
             if($val->fails()){
                return  $this->resp($this->valResult($val));
             }
          }*/
         /* foreach($this->request->filer as $item){
             
          }*/
          return $this->successMessage();
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    