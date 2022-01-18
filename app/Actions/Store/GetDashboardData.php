<?php

namespace App\Actions\Store;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Order;
use App\Models\Product;
use App\Models\SubOrder;
use App\Services\WalletServices\StoreWallet;
use App\Traits\HasResourceStatus;
use App\Traits\HasStore;
use App\Traits\HasWalletFilters;

class GetDashboardData extends Action
{
    use HasStore,HasWalletFilters,HasResourceStatus;
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
            'store_id' => $this->storeIdValidationRule(),
            'start_date' => 'nullable|date',
            'in_range' => 'nullable|integer',
            'end_date' => 'nullable|date|after:start_date',
        ]);
        return $this->valResult($val);
    }

    protected function getCompletedSubOrdersQuery($store_id){
        $query = SubOrder::where('store_id',$store_id)->where('status',Order::STATUS_COMPLETED);
        $query = $this->filterSelectByDate($query);
        $query = $query->with([
            'user:id,first_name,last_name,email'
        ])->orderBy('id','desc');
        return $query;
    }

    protected function getTotalActiveProducts($store_id){
        $query = Product::where('product_status',$this->getResourceActiveId())->where('store_id',$store_id);
        $query = $query->select('id','amount_in_stock','product_type','regular_price','sales_price');
        $query = $query->with(['variations:id,amount_in_stock,regular_price,sales_price,product_id']);
        return $query->get();
    }

    protected function getCurrentPrice($item){
        if($item->sales_price === null || $item->sales_price <= 0){
            return $item->regular_price ?? 0;
        } else {
            return $item->sales_price;
        }
    }

    protected function getTotalStockData($products){
        $data = ['stock_quantity' => 0, 'stock_value' => 0,'total_products' => 0];
        if(count($products) > 0){
            $products->each(function($item)use(&$data){
                if($item->product_type == Product::variation_product_type || count($item->variations) > 0){
                    $item->variations->each(function($variation)use(&$data){
                        $current_price = $this->getCurrentPrice($variation);
                        $data['stock_quantity'] += ($variation->amount_in_stock ?? 0);
                        $data['stock_value'] += ($variation->amount_in_stock ?? 0) * $current_price;
                        $data['total_products'] += 1;
                    });
                } else {
                    $current_price = $this->getCurrentPrice($item);
                    $data['stock_quantity'] += $item->amount_in_stock ?? 0;
                    $data['stock_value'] += ($item->amount_in_stock ?? 0) * $current_price;
                    $data['total_products'] += 1;
                }
            });
        }
        return $data;
    }

    protected function generateRevenueData($store_id){
        $revenues = ['daily_revenue' => 0, 'monthly_revenue'=> 0, 'yearly_revenue' => 0, 'all_time_revenue' => 0];
        $wallet = new StoreWallet($store_id);
        $revenues['daily_revenue'] = $wallet->getCurrentDayCredits();
        $revenues['monthly_revenue'] = $wallet->getCurrentMonthCredits();
        $revenues['yearly_revenue'] = $wallet->getCurrentYearCredits();
        $revenues['all_time_revenue'] = $wallet->getTotalUnLockedCredits();
        return $revenues;
    }
    
    

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $store_id = $this->request->query('store_id');
            $limit = $this->request->query('limit',30);
            $orders_query = $this->getCompletedSubOrdersQuery($store_id);
            $products = $this->getTotalActiveProducts($store_id);
            $stock_data = $this->getTotalStockData($products);
            $data = $orders_query->paginate($limit);
            $data = collect([
                'stock_data'=>$stock_data,
                'total_completed_orders' => $orders_query->count(),
                'revenues' => $this->generateRevenueData($store_id)
            ])->merge($data);
            return $this->successWithData($data);

        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
