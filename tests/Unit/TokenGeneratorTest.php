<?php

namespace Tests\Unit;

use App\Traits\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    use TokenGenerator;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function testCreateNumberToken(){
        $this->assertEquals(20,strlen($this->createNumberToken(20)));
    }

    public function testGeneratePassword(){
        $passlen10 = $this->generatePassword(10);
        echo $passlen10;
        $this->assertEquals(10,strlen($passlen10));
    }
}
