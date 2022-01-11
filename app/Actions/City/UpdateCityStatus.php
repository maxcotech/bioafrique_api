<?php

namespace App\Actions\City;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\City;
use App\Traits\HasRoles;

class UpdateCityStatus extends Action
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
            'status' => 'required|integer'
        ]);
        return $this->valResult($val);
    }

    protected function onChangeStatus(){
        City::where('id',$this->request->id)->update([
            'status' => $this->request->status
        ]);
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            if($this->isSuperAdmin()){
                $this->onChangeStatus();
                return $this->successMessage('City status was updated successfully.');
            }
            return $this->notAuthorized('You are not authorized');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
