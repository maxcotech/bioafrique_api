<?php

namespace App\Actions\WithdrawalRequest;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Exceptions\InvalidRequestStatus;
use App\Models\WithdrawalRequest;
use App\Services\WalletServices\StoreWallet;
use Illuminate\Support\Facades\DB;

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
        $request_id = $this->request->id;
        $status = $this->request->status;

        DB::transaction(function()use($status,$request_id){
            $request = WithdrawalRequest::find($request_id);
            if($status == WithdrawalRequest::STATUS_COMPLETED){
                if($request->status == WithdrawalRequest::STATUS_PENDING){
                    $wallet = new StoreWallet($request->store_id);
                    $wallet->debitWallet($request->amount);
                    WithdrawalRequest::where('id',$request->id)->update(['status' => $status]);
                } else {
                    throw new InvalidRequestStatus('You can not directly mark a non-pending request as complete.');
                }
            } else {
                WithdrawalRequest::where('id',$request->id)->update(['status' => $status]);
            }
        });
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $this->onUpdateStatus();
            return $this->successMessage('Request Status was updated successfully.');
        } 
        catch(InvalidRequestStatus $e){
            return $this->validationError($e->getMessage());
        }
        catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
