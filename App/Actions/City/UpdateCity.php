<?php

namespace App\Actions\City;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\City;
use App\Traits\HasRoles;
use Illuminate\Validation\Rule;

class UpdateCity extends Action
{
    use HasRoles;
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'id' => 'required|integer|exists:cities,id',
            'state_id' => 'required|integer|exists:states,id',
            'city_name' => ['required','string',Rule::unique('cities','city_name')->where(function($query){
                $query->where('state_id',$this->request->state_id)->where('id',"!=",$this->request->id);
            })],
            'city_code' => ['nullable','string',Rule::unique('cities','city_code')->where(function($query){
                $query->where('state_id',$this->request->state_id)->where('id',"!=",$this->request->id);
            })]
        ]);
        return $this->valResult($val);
    }

    protected function onUpdateCity(){
        City::where('id',$this->request->id)->update(
            $this->request->all(['state_id','city_name','city_code'])
        );
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            if($this->isSuperAdmin()){
                $this->onUpdateCity();
                return $this->successMessage('City was updated successfully.');
            }
            return $this->notAuthorized('You are not authorized.');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
