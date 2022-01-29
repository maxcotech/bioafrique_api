<?php

namespace App\Actions\ContactSupport;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ContactMessage;

class CreateMessage extends Action
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'email_address' => 'required|email',
            'message' => 'required|string'
        ]);
        return $this->valResult($val);
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $data = $this->request->all(['email_address','message']);
            ContactMessage::create($data);
            return $this->successMessage('Support request sent successfully, you will be notified by email when an agent replies.');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
