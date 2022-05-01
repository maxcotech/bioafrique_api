<?php

namespace App\Actions\WithdrawalRequest;

use App\Exceptions\InvalidBankCode;
use App\Exceptions\InvalidBankDetails;
use App\Exceptions\InvalidTransferGateway;
use App\Exceptions\MaximumAmountExceeded;
use App\Exceptions\TransferFailed;
use App\Models\StoreBankAccount;
use App\Models\WithdrawalRequest;
use App\Services\WalletServices\StoreWallet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MassSettle extends Settle
{
    protected $request;
    protected $current_request_id;
    protected StoreWallet $store_wallet;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'currency_id' => 'required|integer|exists:currencies,id',
            'max_amount' => 'required|numeric',
            'gateway_id' => 'required|integer'
        ]);
        return $this->valResult($val);
    }

    protected function getRequests(){
        $bank_ids = StoreBankAccount::where('bank_currency_id',$this->request->currency_id)->pluck('id');
        $query = WithdrawalRequest::whereIn("bank_account_id",$bank_ids);
        $query = WithdrawalRequest::where('status',WithdrawalRequest::STATUS_PENDING);
        $query = $query->orderBy('id','desc');
        $query = $query->with([
            'bank:id,account_number,bank_code,bank_currency_id',
            'bank.currency:id,currency_name,currency_code,base_rate'
        ]);
        return $query->get();
    }

    protected function onMassSettle($requests,$gateway_id){
        $max_amount = $this->request->max_amount;
        foreach($requests as $request){
            if($max_amount > $request->amount){
                $this->current_request_id = $request->id;
                if($this->onSettle($request,$gateway_id)){
                    $max_amount = $max_amount - $request->amount;
                }
            } else {
                throw new MaximumAmountExceeded("The maximum amount you specified for transfers has been exhaused. Please set new amount and try again.");
            }
        }
        return true;
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $requests = $this->getRequests();
            $gateway_id = $this->request->gateway_id;
            if(count($requests) < 1) return $this->validationError('Could not find any requests that matches the selected currency.');
            if($this->onMassSettle($requests,$gateway_id)){
                return $this->successMessage("Vendors' withdrawal request settlements has been completed.");
            } else {
                return $this->internalError("An Error occurred.");
            }

        } 
        catch(InvalidBankCode $e){
            return $this->validationError($e->getMessage());
        }
        catch(InvalidBankDetails $e){
            return $this->validationError($e->getMessage());
        }
        catch(TransferFailed $e){
            if(isset($this->store_wallet) && isset($this->current_request_id)){
                DB::table('withdrawal_requests')->where('id',$this->current_request_id)->update(
                    ['reference'=>$this->store_wallet->createWithdrawalRef()]);
            }
            return $this->internalError($e->getMessage());
        }
        catch(InvalidTransferGateway $e){
            return $this->validationError($e->getMessage());
        }
        catch(MaximumAmountExceeded $e){
            return $this->internalError($e->getMessage());
        }
        catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
