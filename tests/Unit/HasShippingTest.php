<?php

namespace Tests\Unit;

use App\Traits\HasShipping;
use PHPUnit\Framework\TestCase;

class HasShippingTest extends TestCase
{
    use HasShipping;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    protected function getFakeDimensionProduct($weight = null, $height = null, $width = null, $length = null){
        return (object) [
            'weight' => $weight,
            'dimension_height' => $height,
            'dimension_width' => $width,
            'dimension_length' => $length
        ];
    }

    public function testGetShippingDimension(){
        $product1 = $this->getFakeDimensionProduct(20,null,null,null);
        $product2 = $this->getFakeDimensionProduct(null,10,20,30);
        $product3 = $this->getFakeDimensionProduct(100,10,20,30);
        $this->assertEquals(20,$this->getShippingDimension($product1));
        $this->assertEquals(round((10 * 20 * 30) / 3500),$this->getShippingDimension($product2));
        $this->assertEquals(100,$this->getShippingDimension($product3));
    }

    public function testGetDimensionRangeRateValue(){
        $product1 = $this->getFakeDimensionProduct(20,null,null,null);
        $product2 = $this->getFakeDimensionProduct(null,10,20,30);
        $product3 = $this->getFakeDimensionProduct(100,10,20,30);
        $dimension1 = $this->getShippingDimension($product1);
        $dimension2 = $this->getShippingDimension($product2);
        $dimension3 = $this->getShippingDimension($product3);
        $ranges = [
            ['min'=>0,'max'=>50,'rate'=>30],
            ['min'=>51,'max'=>100,'rate'=>60],
            ['min'=>101,'max'=>150,'rate'=>90]
        ];
        $this->assertEquals(30,$this->getDimensionRangeRateValue($dimension1,$ranges));
        $this->assertEquals(30,$this->getDimensionRangeRateValue($dimension2,$ranges));
        $this->assertEquals(60,$this->getDimensionRangeRateValue($dimension3,$ranges));
    }
}


