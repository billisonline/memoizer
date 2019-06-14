<?php

namespace BYanelli\Memoizer\Tests;

use BYanelli\Memoizer\MemoizationHasher;
use PHPUnit\Framework\TestCase;

class MemoizationHasherTest extends TestCase
{
    private function hash($val): string
    {
        return MemoizationHasher::getInstance()->hash($val);
    }

    public function testSameScalarValuesHaveSameHashes()
    {
        $this->assertEquals($this->hash(1), $this->hash(1));
        $this->assertEquals($this->hash(1.0), $this->hash(1.0));
        $this->assertEquals($this->hash('foo'), $this->hash('foo'));
        $this->assertEquals($this->hash(true), $this->hash(true));
    }

    public function testDifferentScalarValuesHaveDifferentHashes()
    {
        $this->assertNotEquals($this->hash(1), $this->hash(2));
        $this->assertNotEquals($this->hash(1.0), $this->hash(2.0));
        $this->assertNotEquals($this->hash('foo'), $this->hash('bar'));
        $this->assertNotEquals($this->hash(true), $this->hash(false));
    }

    public function testDifferentlyTypedScalarValuesHaveDifferentHashes()
    {
        $this->assertNotEquals($this->hash(1), $this->hash('1'));
        $this->assertNotEquals($this->hash(1.0), $this->hash(1));
        $this->assertNotEquals($this->hash(true), $this->hash(1));
    }
}