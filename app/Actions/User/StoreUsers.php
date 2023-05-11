<?php
namespace App\Actions\User;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Order;
use App\Models\SubOrder;
use App\Models\User;
use App\Traits\HasArrayOperations;
use App\Traits\HasStore;
use Illuminate\Support\Facades\Validator;

class StoreUsers extends Action{
    use HasStore,HasArrayOperations;
    protected $request;
    public function __construct(Request $request){
        $this->request=$request;
    }

    protected function validate(){
        $val = Validator::make($this->request->all(),[
            'store_id' => $this->storeIdValidationRule(),
            'limit' => 'nullable|integer'
        ]); 
        return $this->valResult($val);
    }

    protected function onGetStoreUsers(){
        $store_id = $this->request->query('store_id');
        $limit = $this->request->query('limit',30);
        $user_ids = SubOrder::where('store_id',$store_id)->where('status',Order::STATUS_COMPLETED)->pluck('user_id');
        $unique_user_ids = $this->removeArrayDuplicates($user_ids);
        $users = User::whereIn('id',$unique_user_ids)->paginate($limit);
        return $users;
    }
    
    public function execute(){
        try{
            $val = $this->validate();
            if($val['status'] !== "success") return $this->resp($val);
            $data = $this->onGetStoreUsers();
            return $this->successWithData($data);
        }
        catch(\Exception $e){
            return $this->internalError($e->getMessage());
        }
    }

}
    