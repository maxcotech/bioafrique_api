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
}
