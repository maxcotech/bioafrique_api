<?php

namespace App\Actions\City;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\City;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;
use Illuminate\Validation\Rule;

class CreateCity extends Action
{
    use HasRoles,HasResourceStatus;
    protected $request,$user;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->user = $request->user();
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'state_id' => 'required|integer|exists:states,id',
            'city_name' => ['required','string',Rule::unique('cities','city_name')->where(function($query){
                $query->where('state_id',$this->request->state_id);
            })],
            'city_code' => ['nullable','string',Rule::unique('cities','city_code')->where(function($query){
                $query->where('state_id',$this->request->state_id);
            })]
        ]);
        return $this->valResult($val);
    }

    protected function getNewStatus(){
        if($this->isSuperAdmin($this->user->user_type)){
            return $this->getResourceActiveId();
        } else {
            return $this->getResourceInReviewId();
        }
    }

    protected function onCreate(){
        $data = $this->request->all(['state_id','city_name','city_code']);
        $data['status'] = $this->getNewStatus();
        City::create($data);
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $this->onCreate();
            return $this->successMessage('City was created successfully.');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
