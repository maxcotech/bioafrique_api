<?php
namespace App\Actions\Product;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Models\RecentlyViewed;
use App\Traits\HasArrayOperations;
use App\Traits\HasAuthStatus;
use App\Traits\HasProduct;
use App\Traits\HasProductReview;
use App\Traits\HasShoppingCartItem;
use Illuminate\Support\Facades\Validator;

class GetRecentlyViewed extends Action{
    use HasAuthStatus,HasShoppingCartItem,HasProduct,HasArrayOperations,HasProductReview;

    protected $request;
    protected $access_type;
    public function __construct(Request $request){
        $this->request=$request;
        $this->access_type = $this->getUserAuthTypeObject($request->user());
    }

    protected function validate(){
        $val = Validator::make($this->request->all(),[
            'limit' => 'nullable|integer',
            'excluded_product_id' => 'nullable|integer|exists:products,id'
        ]);
        return $this->valResult($val);
    }

    protected function getRecentlyAddedRecords(){
        $limit = $this->request->query('limit',15);
        $excluded_id = $this->request->query('excluded_product_id',null);
        $query = RecentlyViewed::where('user_id',$this->access_type->id)->where('user_type',$this->access_type->type);
        if(isset($excluded_id)){
            $query = $query->where('product_id',"!=",$excluded_id);
        }
        $query = $query->select('id','product_id','last_viewed');
        $query = $query->orderBy('last_viewed','desc');
        return $query->paginate($limit);
    }

    protected function retrieveProductIds($records){
        $product_ids = [];
        $records->each(function($item)use(&$product_ids){
            if(!in_array($item->product_id,$product_ids)){
                array_push($product_ids,$item->product_id);
            }
            return $item;
        });
        return $product_ids;
    }

    protected function retrieveProductsByIdArray($product_ids){
        $data = Product::whereIn('id',$product_ids)
        ->select('id','product_name','product_image','sales_price','regular_price','product_slug')
        ->with([
            'variations:id,regular_price,sales_price,variation_name,product_id,variation_image'
        ])->get();
        $data = $this->appendReviewAverage($data);
        $data = $this->appendCartQuantityToEachItem($data,$this->access_type);
        $data = $this->appendWishListStatus($data,$this->access_type);
        return $data;
    }


    protected function appendProductsAndVariations($records){
        $product_ids = $this->retrieveProductIds($records);
        $products = $this->retrieveProductsByIdArray($product_ids);
        $records->each(function($item)use($products){
            $item->product = $this->selectArrayItemByKeyPair("id",$item->product_id,$products);
        });
        return $records;
    }
    
    public function execute(){
        try{
            $val = $this->validate();
            if($val['status'] !== "success") return $this->resp($val);
            $records = $this->getRecentlyAddedRecords();
            $records = $this->appendProductsAndVariations($records);
            return $this->successWithData($records);
        }
        catch(\Exception $e){
            return $this->internalError($e->getMessage());
        }
    }

}
    