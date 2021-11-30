<?php

namespace Tests\Unit;

use App\Traits\HasArrayOperations;
use PHPUnit\Framework\TestCase;

class HasArrayOperationsTest extends TestCase
{
    use HasArrayOperations;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function testSumArrayValuesByKey(){
        $arr = [
            [ 'key' => 50,'other_key' => 60],
            [ 'key' => 50,'other_key' => 60],
            [ 'key' => 50,'other_key' => 60]
        ];
        $arr2 = (object) $arr;
        $this->assertEquals(150,$this->sumArrayValuesByKey($arr,"key"));
        $this->assertEquals(150,$this->sumArrayValuesByKey($arr2,"key"));
        $this->assertEquals(180,$this->sumArrayValuesByKey($arr,"other_key"));
        $this->assertEquals(180,$this->sumArrayValuesByKey($arr,"other_key"));
        $this->assertEquals(0,$this->sumArrayValuesByKey(null,"key"));
    }

    public function testSelectArrayItemByKeyPair(){
        //params $key,$value,$array
        $in_data = [
            [ 'key' => 1,'gin' => 'manu'],
            [ 'key' => 2,'gin' => 'menu'],
            [ 'key' => 3,'gin' => 'menuio']
        ];
       $in_data2 = (object) $in_data;
       $this->assertEqualsCanonicalizing(['key' => 2, 'gin' => 'menu'],$this->selectArrayItemByKeyPair("key",2,$in_data));
       $this->assertEqualsCanonicalizing(['key' => 2, 'gin' => 'menu'],$this->selectArrayItemByKeyPair("gin","menu",$in_data));
       $this->assertEqualsCanonicalizing(['key' => 2, 'gin' => 'menu'],$this->selectArrayItemByKeyPair("key",2,$in_data2));
       $this->assertEqualsCanonicalizing(['key' => 2, 'gin' => 'menu'],$this->selectArrayItemByKeyPair("gin","menu",$in_data2));
    }

    public function testExtractUniqueValueList(){
        $in_data = [
            ['key' => 1,'gin' => 'manu'],
            ['key' => 1,'gin' => 'menu'],
            ['key' => 2,'gin' => 'menu']
        ];
        $in_data2 = (object) $in_data;
        $in_data3 = [
            [
                "id" => 8,
                "user_id"=> 3,
                "user_type"=> "App\\Models\\User",
                "item_id"=> 16,
                "variant_id"=> null,
                "store_id"=> 11,
                "item_type"=> "simple_product",
                "quantity"=> 1,
                "created_at"=> "2021-11-27T07:01:48.000000Z",
                "updated_at"=> "2021-11-27T07:02:39.000000Z"
            ]
        ];
        $this->assertEqualsCanonicalizing([1,2],$this->extractUniqueValueList($in_data,"key"));
        $this->assertEqualsCanonicalizing(['manu','menu'],$this->extractUniqueValueList($in_data,"gin"));
        $this->assertEqualsCanonicalizing([1,2],$this->extractUniqueValueList($in_data2,"key"));
        $this->assertEqualsCanonicalizing(['manu','menu'],$this->extractUniqueValueList($in_data2,"gin"));
        $this->assertEqualsCanonicalizing([11],$this->extractUniqueValueList($in_data3,"store_id"));
    }
}
