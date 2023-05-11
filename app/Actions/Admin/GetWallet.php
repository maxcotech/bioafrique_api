<?php
namespace App\Actions\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\SuperAdminWallet;
use App\Services\WalletServices\SuperAdminWallet as AdminWalletService;
use App\Traits\HasWalletFilters;

class GetWallet extends Action{
   use HasWalletFilters;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'limit' => 'nullable|integer',
         'start_date' => 'nullable|date',
         'in_range' => 'nullable|integer',
         'end_date' => 'nullable|date|after:start_date',
         'ledger_type' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getWalletData(){
      $query = SuperAdminWallet::orderBy('id','desc');
      $query = $this->filterSelectByDate($query);
      $query = $this->filterSelectByLedgerType($query);
      $query = $query->with(['orderCommissionLock:id,status,wallet_fund_id']);
      $data = $query->paginate($this->request->query('limit',15));
      $data->each(function($item){
         $item->sender_type_text = SuperAdminWallet::getSenderTypeText($item->sender_type);
         $item->sender_email = SuperAdminWallet::getSenderEmail($item->sender_type,$item->sender_id);
         $item->ledger_type_text = SuperAdminWallet::getLedgerTypeText($item->ledger_type);
         $item->transaction_type_text = SuperAdminWallet::getTransactionType($item->transaction_type);
         return $item;
      });
      return $data;
   }

   protected function appendAnalyticsData($data){
      $wallet = new AdminWalletService();
      return collect([
         'total_balance' => $wallet->getTotalAccountBalance(),
         'locked_credits' => $wallet->getTotalLockedCredits(),
         'unlocked_credits' => $wallet->getTotalUnLockedCredits(),
         'total_debits' => $wallet->getTotalDebits()
      ])->merge($data);
   }



   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $data = $this->getWalletData();
         $data = $this->appendAnalyticsData($data);
         return $this->successWithData($data);
      
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   