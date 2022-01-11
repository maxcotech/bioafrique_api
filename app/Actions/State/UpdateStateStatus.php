<?php

namespace App\Actions\State;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\State;

class UpdateStateStatus extends Action
{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    
    protected function validate(){
        $val = Validator::make($this->request->all(), [
            'id' => 'required|integer|exists:states,id',
            'status' => 'required|integer'
        ]);
        return $this->valResult($val);
    }

    public function execute(){
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            State::where('id',$this->request->id)
            ->update(['status' => $this->request->status]);
            return $this->successMessage('State status updated successfully.');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
