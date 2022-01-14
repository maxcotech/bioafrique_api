<?php

namespace Tests\Unit;

use App\MockData\MockProductRating;
use App\Traits\HasProductReview;
use PHPUnit\Framework\TestCase;

class HasDataProcessingTest extends TestCase
{
    use HasProductReview;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGetReviewSummary(){
        $expected = ["5" => 1,"4" => 1,"3" => 1,"2" => 1,"1" => 0,"0" => 0];
        $this->assertEqualsCanonicalizing(
            $expected,
            $this->getReviewSummary(
                json_decode(MockProductRating::fourRows),
                "star_rating",
            )
        );
    }

    public function testGetReviewAverage(){
        $data1 = json_decode(MockProductRating::fourRows);
        $data2 = json_decode(MockProductRating::allRows);
        $result = $this->getReviewAverage($data1,"star_rating");
        $result2 = $this->getReviewAverage($data2,"star_rating");
        echo $result2;
        $this->assertEquals(14/4,$result);
    }
   
}
