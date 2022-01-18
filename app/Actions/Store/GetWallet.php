<?php

namespace App\Actions\Store;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreWallet;
use App\Services\WalletServices\StoreWallet as StoreWalletService;
use App\Traits\HasRoles;
use App\Traits\HasStore;
use App\Traits\HasWalletFilters;

class GetWallet extends Action
{
    use HasStore,HasRoles,HasWalletFilters;
    protected $request;
    protected $user;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->user = $request->user();
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'store_id' => $this->getStoreIdRules(),
            'limit' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'in_range' => 'nullable|integer',
            'end_date' => 'nullable|date|after:start_date',
            'ledger_type' => 'nullable|integer'
        ]);
        return $this->valResult($val);
    }

    protected function getStoreIdRules(){
        $user_type = $this->user->user_type;
        if($this->isSuperAdmin($user_type)){
            return 'required|integer|exists:stores,id';
        } else {
            return $this->storeIdValidationRule();
        }
    }

    protected function onGetWalletData(){
        $store_id = $this->request->query('store_id');
        $limit = $this->request->query('limit',30);
        $query = StoreWallet::where('store_id',$store_id);
        $query = $query->orderBy('id','desc');
        $query = $this->filterSelectByDate($query);
        $query = $this->filterSelectByLedgerType($query);
        $query = $query->with(['lock:id,status,wallet_fund_id']);
        $data = $query->paginate($limit);
        $data->each(function($item){
            $item->sender_type_text = StoreWallet::getSenderTypeText($item->sender_type);
            $item->sender_email = StoreWallet::getSenderEmail($item->sender_type,$item->sender_id);
            $item->ledger_type_text = StoreWallet::getLedgerTypeText($item->ledger_type);
            $item->transaction_type_text = StoreWallet::getTransactionType($item->transaction_type);
            return $item;
        });
        return $data;
    }

    protected function appendBalanceSummary($data){
        $wallet = new StoreWalletService($this->request->store_id);
        $new_data = collect([
            'total_balance' => $wallet->getTotalAccountBalance(),
            'locked_credits' => $wallet->getTotalLockedCredits(),
            'unlocked_credits' => $wallet->getTotalUnLockedCredits(),
            'total_debits' => $wallet->getTotalDebits(),
            'pending_requests' => $wallet->getTotalPendingWithdrawal()
        ])->merge($data);
        return $new_data;
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $data = $this->onGetWalletData();
            $data = $this->appendBalanceSummary($data);
            return $this->successWithData($data);
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
