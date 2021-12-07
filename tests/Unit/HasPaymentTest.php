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
    public function testCalculateCommissionAmount(){
        $amount = 200;
        $commission = 10;
        $this->assertEquals(20,$this->calculateCommissionAmount($amount,$commission));
    }

    public function testGetCommissionAndRemainderAmount(){
        $amount = 200;
        $commission = 10;
        $expected = (object) [
            'commission_amount' => 20,
            'remainder' => 180
        ];
        $this->assertEquals($expected,$this->getCommissionAndRemainderAmount($amount,$commission));
    }

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
