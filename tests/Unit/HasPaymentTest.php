<?php

namespace Tests\Unit;

use App\Traits\HasPayment;
use PHPUnit\Framework\TestCase;

class HasPaymentTest extends TestCase
{
    use HasPayment;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testSumPriceInTwoRelatedArraysByKeys(){
        $arr1 = [
            ['key'=>2,'price1'=>2],['key'=>4,'price1'=>4]
        ];
        $arr2 = [
            ['key3'=>4,'price2'=>5],['key3'=>2,'price2'=>10]
        ];
        $obj1 = (object) $arr1;
        $obj2 = (object) $arr2;
        $this->assertEqualsCanonicalizing(
            [
                ["relation_key"=>2,"summation_value"=>12],
                ["relation_key"=>4,"summation_value"=>9]
            ],
            $this->sumPriceInTwoRelatedArraysByKeys(
                $arr1,$arr2,"price1","price2","key","key3"
            )
        );
        $this->assertEqualsCanonicalizing(
            [
                ["relation_key"=>2,"summation_value"=>12],
                ["relation_key"=>4,"summation_value"=>9]
            ],
            $this->sumPriceInTwoRelatedArraysByKeys(
                $obj1,$obj2,"price1","price2","key","key3"
            )
        );

    }
}
