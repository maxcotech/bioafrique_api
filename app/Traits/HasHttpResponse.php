<?php
namespace App\Traits;

trait HasHttpResponse{
    public function payload($status='success',$data=[],$code=200,$message=''){
        return ['status'=>$status,'code'=>$code,'message'=>$message,'data'=>$data];
    }
    public function validationError($error){
        return response()->json(['status'=>'validation_failed','code'=>403,'data'=>[],'message'=>$error],403);
    }
    public function internalError($message){
        return response()->json(['status'=>'failed','code'=>500,'message'=>$message,'data'=>" "],500);
    }
    public function successWithData($data,$message="successful"){
        return response()->json(['status'=>'success','code'=>200,'message'=>$message,'data'=>$data],200);
    }
    public function successMessage($message="successful"){
        return response()->json(['status'=>'success','code'=>200,'message'=>$message,'data'=>[]],200);
    }
    public function notAuthorized($message='You are not authorized to carry out this operation'){
        return response()->json(['status'=>'not_authorized','code'=>401,
                'message'=>$message,'data'=>[]],401);
    }
    public function notFoundError($message='Requested Resource Not Found'){
        return response()->json(['status'=>'not_found','code'=>404,
        'message'=>$message,'data'=>[]]);
    }
    public function valResult($val){
        if($val->fails()){
            $errors = $this->buildError($val);
            return ['status'=>'validation_failed','code'=>'403','message'=>$errors, "data" => $val->failed()];
        }
        else{
            return ['code' => 200, 'status'=>'success','message'=>""];
        }
    }
    public function valMessageObject($message){
        return $this->payload('validation_failed',[],'403',$message);
    }
    public function resp($resp){
        return response()->json($resp,$resp['code']);
    }
    public function valResultResp($val){
        if($val->fails()){
            return $this->validationError($this->buildError($val));
        }
        else{
            return $this->successMessage();
        } 
    }
    public function errorWithData($data,$message="failed",$code=500,$status='failed'){
        return response()->json(['status'=>$status,'code'=>$code,'message'=>$message,'data'=>$data],$code);
    }
    public function buildError($validation){
        $errorString="";
        $errors=json_decode(json_encode($validation->errors()),true);
        foreach($errors as $key=>$val){
            $errorString .= $val[0]."\n ";
        }
        return $errorString;
    }

}
