<?php
namespace App\Traits;

use App\Models\ProductReview;
use Countable;
use Illuminate\Database\Eloquent\Collection;

trait HasProductReview{
    use HasResourceStatus;
    protected function getReviewSummary($data,$rating_key){
        $output = [
            'count_0' => 0,'count_1' => 0,'count_2' => 0,'count_3' => 0,'count_4' => 0,'count_5' => 0
        ];
        if(!is_object($data) && !is_array($data)) return $output;
        $data = json_decode(json_encode($data),true);
        if(count($data) == 0) return $output;
        foreach($data as $row){
            $output["count_".strval($row[$rating_key])] += 1;
        }
        return $output;
    }

    protected function getReviewAverage($data, $rating_key){
        $data = json_decode(json_encode($data),true);
        $data_length = count($data);
        if($data_length == 0) return 0;
        $total_rating = 0;
        foreach($data as $row){
            $total_rating += $row[$rating_key];
        }
        return $total_rating / $data_length;
    }

    protected function getReviewByProductList($products,array $selects = []){
        $product_ids = [];
        $products->each(function($item)use(&$product_ids){
            if(!in_array($item->id,$product_ids)){
                array_push($product_ids,$item->id);
            }
        });
        $query = ProductReview::whereIn('product_id',$product_ids)
        ->where('status',$this->getResourceActiveId());
        return (isset($selects)) ? $query->get($selects): $query->get();
    }

    protected function appendReviewAverage($products){
        if(count($products) === 0) return $products;
        $reviews = $this->getReviewByProductList($products,['product_id','star_rating']);
        $products->each(function($item)use($reviews){
            $product_reviews = [];
            foreach($reviews as $review){
                if($review->product_id === $item->id){
                    array_push($product_reviews,$review);
                }
            }
            $item->review_average = $this->getReviewAverage($product_reviews,"star_rating");
            return $item;
        });
        return $products;
    }

}
    