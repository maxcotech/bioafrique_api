<?php

namespace App\Actions\ContactSupport;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ContactMessage;

class GetMessages extends Action
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'limit' => 'nullable|integer',
            'seen' => 'nullable|integer|min:0,max:1'
        ]);
        return $this->valResult($val);
    }

    protected function onGetMessage(){
        $seen = $this->request->query('seen',null);
        $limit = $this->request->query('limit',15);
        $contacts = new ContactMessage();
        $contacts = $contacts->orderBy('id','desc');
        if(isset($seen)){
            $contacts = $contacts->where('seen',$seen);
        }
        return $contacts->paginate($limit);
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $messages = $this->onGetMessage();
            return $this->successWithData($messages);
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
