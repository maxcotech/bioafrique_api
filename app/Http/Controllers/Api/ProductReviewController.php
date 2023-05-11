<?php

namespace App\Http\Controllers\Api;

use App\Actions\ProductReview\CreateReview;
use App\Actions\ProductReview\DeleteReview;
use App\Actions\ProductReview\GetReviews;
use App\Actions\ProductReview\PendingReviews;
use App\Actions\ProductReview\UpdateReview;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function create(Request $request){
        return (new CreateReview($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateReview($request))->execute();
    }
    public function index(Request $request, $review_id = null){
        return (new GetReviews($request,$review_id))->execute();
    }
    public function delete(Request $request,$review_id){
        return (new DeleteReview($request,$review_id))->execute();
    }
    public function getPendingReviews(Request $request){
        return (new PendingReviews($request))->execute();
    }
}
