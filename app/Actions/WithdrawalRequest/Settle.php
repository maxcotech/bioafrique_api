<?php

namespace App\Actions\WithdrawalRequest;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\WithdrawalRequest;
use App\Exceptions\InvalidBankCode;
use App\Exceptions\InvalidBankDetails;
use App\Exceptions\InvalidTransferGateway;
use App\Exceptions\TransferFailed;
use App\Services\PaymentService;
use App\Services\Utilities\BankPayload;
use App\Services\WalletServices\StoreWallet;
use App\Traits\HasRateConversion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Settle extends Action
{
    use HasRateConversion;
    protected $request;
    protected $user;
    protected StoreWallet $store_wallet;
    protected $request_id;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->user = $request->user();
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'request_id' => ['required','integer',Rule::exists('withdrawal_requests','id')->where(function($query){
                $query->where('status',WithdrawalRequest::STATUS_PENDING);
            })],
            'gateway_id' => 'required|integer'
        ]);
        return $this->valResult($val);
    }

    protected function onSettle($request,$gateway_id){
        if($request->bank->bank_code === null){
            throw new InvalidBankCode("The current vendor settlement gateway requires the vendor to use a bank with valid African bank code. For vendors outside Africa, you can carry out the settlement manually then mark their requests as settled on complete.");
        }
        $base_amount = $this->userToBaseCurrency($request->amount,$this->user);
        $bank = $request->bank;
        $bank_currency = $bank->currency;
        $bank_amount = $this->convertBaseAmountByRate($base_amount,$bank_currency->base_rate);
        $payment_service = new PaymentService($gateway_id,$request->reference,$bank_currency->currency_code,$bank_amount);
        $bank_payload = new BankPayload($bank->bank_code,$bank->account_number,$bank_currency->currency_code,null,null);
        $this->store_wallet = new StoreWallet($request->store_id);
        if($payment_service->transferFunds($bank_payload)){
            DB::transaction(function()use($request){
                WithdrawalRequest::where('id',$request->id)->update(['status'=>WithdrawalRequest::STATUS_COMPLETED]);
                $this->store_wallet->debitWallet($request->amount);
            });
            return true;
        }
        return false;
    }

    protected function getRequest($request_id){
        $query = WithdrawalRequest::where("id",$request_id);
        $query = $query->with([
            'bank:id,account_number,bank_code,bank_currency_id',
            'bank.currency:id,currency_name,currency_code,base_rate'
        ]);
        return $query->first();
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $request_id = $this->request->request_id;
            $gateway_id = $this->request->gateway_id;
            $request = $this->getRequest($request_id);
            if($this->onSettle($request,$gateway_id)){
                return $this->successMessage('Vendor settlement completed successfully.');
            }
            return $this->internalError('Failed to complete vendor settlement');
        } 
        catch(InvalidBankCode $e){
            return $this->validationError($e->getMessage());
        }
        catch(InvalidBankDetails $e){
            return $this->validationError($e->getMessage());
        }
        catch(TransferFailed $e){
            if(isset($this->store_wallet)){
                DB::table('withdrawal_requests')->where('id',$this->request->request_id)->update(
                    ['reference'=>$this->store_wallet->createWithdrawalRef()]);
            }
            return $this->internalError($e->getMessage());
        }
        catch(InvalidTransferGateway $e){
            return $this->validationError($e->getMessage());
        }
        catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
