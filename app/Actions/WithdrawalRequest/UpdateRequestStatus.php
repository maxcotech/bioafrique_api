<?php

namespace App\Actions\WithdrawalRequest;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\WithdrawalRequest;

class UpdateRequestStatus extends Action
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'status' => 'required|integer',
            'id' => 'required|integer|exists:withdrawal_requests,id'
        ]);
        return $this->valResult($val);
    }

    protected function onUpdateStatus(){
        $request_id = $this->request->query('id');
        $status = $this->request->query('status');
        WithdrawalRequest::where('id',$request_id)->update([
            'status' => $status
        ]);
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $this->onUpdateStatus();
            return $this->successMessage('Request Status was updated successfully.');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
