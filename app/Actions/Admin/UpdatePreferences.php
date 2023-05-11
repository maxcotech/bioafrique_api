<?php

namespace App\Actions\Admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\SuperAdminPreference;
use App\Traits\HasRoles;

class UpdatePreferences extends Action
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
            'data' => 'required|json'
        ]);
        return $this->valResult($val);
    }

    protected function validateDataContent($data){
        if(count($data) == count(SuperAdminPreference::initData)){
            foreach($data as $sub_item){   
                $key = $sub_item['preference_key'] ?? null;
                $value = $sub_item['preference_value'] ?? null;
                if(!isset($key)) return $this->valMessageObject('Preference key is required.');
                if(!SuperAdminPreference::where('preference_key',$key)->exists()){
                    return $this->valMessageObject("The key $key you submitted is not a valid preference key.");
                }
                if(!isset($value) || $value === ""){
                    return $this->valMessageObject("The value of $key is required.");
                }
            }
            return $this->payload();
        }
        return $this->valMessageObject('The number of preferences you submitted are less than or greater than the expected number.');
    }

    protected function onUpdatePreferences($data){
        foreach($data as $sub_item){
            $key = $sub_item['preference_key'] ?? null;
            $value = $sub_item['preference_value'] ?? null;
            SuperAdminPreference::where('preference_key',$key)->update([
                'preference_value' => $value
            ]);
        }
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $data = json_decode($this->request->data,true);
            $val2 = $this->validateDataContent($data);
            if ($val2['status'] !== "success") return $this->resp($val2);
            if($this->isSuperAdmin()){
                $this->onUpdatePreferences($data);
                return $this->successMessage('Admin Preferences updated successfully.');
            }
            return $this->notAuthorized('You are not authorized.');

        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
