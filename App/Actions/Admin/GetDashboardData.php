<?php
namespace App\Actions\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;

class GetDashboardData extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'status' => 'nullable|integer',
         'limit' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getOrders(){
      $limit = $this->request->query('limit',20);
      $status = $this->request->query('status',null);
      $query = Order::orderBy('id','desc');
      if(isset($status)){
         $query = $query->where('status',$status);
      }
      $query = $query->with([
         'user:id,first_name,last_name,email'
      ]);
      $data = $query->paginate($limit);
      $data->append(['converted_amount']);
      return $data;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $orders = $this->getOrders();
         $data = collect([
            'total_users' => User::count(),
            'total_stores' => Store::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count()
         ])->merge($orders);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   