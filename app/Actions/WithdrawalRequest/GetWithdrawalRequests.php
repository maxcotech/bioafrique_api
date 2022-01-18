<?php

namespace App\Actions\WithdrawalRequest;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreBankAccount;
use App\Models\WithdrawalRequest;

class GetWithdrawalRequests extends Action
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
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'status' => 'nullable|integer'
        ]);
        return $this->valResult($val);
    }


    protected function filterByCurrency($query,$currency_id){
        if(isset($currency_id)){
            $bank_ids = StoreBankAccount::where('bank_currency_id',$currency_id)->pluck('id');
            $query = $query->whereIn('bank_account_id',$bank_ids);
        }
        return $query;
    }

    protected function onGetRequests(){
        $status = $this->request->query('status',WithdrawalRequest::STATUS_PENDING);
        $currency_id = $this->request->query('currency_id',null);
        $limit = $this->request->query('limit',30);
        $query = WithdrawalRequest::where('status',$status);
        $query = $this->filterByCurrency($query,$currency_id);
        if(isset($status)){
            $query = $query->where('status',$status);
        }
        $query = $query->with([
            'store:id,store_name,store_email',
            'bank:id,bank_name,account_number,bank_code,store_id,bank_currency_id',
            'bank.currency:id,currency_name,currency_code'
        ]);
        return $query->paginate($limit);

    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $data = $this->onGetRequests();
            return $this->successWithData($data);
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
