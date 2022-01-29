<?php

namespace App\Actions\ContactSupport;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ContactMessage;

class ChangeSeenStatus extends Action
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'status' => 'required|integer|min:0,max:1',
            'id' => 'required|integer|exists:contact_messages,id'
        ]);
        return $this->valResult($val);
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            ContactMessage::where('id',$this->request->id)->update([
                'seen' => $this->request->status
            ]);
            return $this->successMessage('Updated Successfully');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
