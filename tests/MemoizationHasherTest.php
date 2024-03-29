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
        $this->assertNotEquals($this->hash(null), $this->hash(0));
    }

    public function testSequentialArraysInSameOrderHaveSameHashes()
    {
        $this->assertEquals($this->hash([1, 2, 3]), $this->hash([1, 2, 3]));
        $this->assertEquals($this->hash(['foo', 'bar', 'baz']), $this->hash(['foo', 'bar', 'baz']));
        $this->assertEquals($this->hash([1, 2, 3, ['foo', 'bar']]), $this->hash([1, 2, 3, ['foo', 'bar']]));
    }

    public function testSequentialArraysInDifferentOrderHaveDifferentHashes()
    {
        $this->assertNotEquals($this->hash([1, 2, 3]), $this->hash([3, 2, 1]));
        $this->assertNotEquals($this->hash(['foo', 'bar', 'baz']), $this->hash(['foo', 'baz', 'bar']));
        $this->assertNotEquals($this->hash([1, 2, 3, ['foo', 'bar']]), $this->hash([1, 2, 3, ['bar', 'foo']]));
    }

    public function testDictionariesWithSameValuesAndKeysInSameOrderHaveSameHashes()
    {
        $this->assertEquals($this->hash(['foo' => 1, 'bar' => 2]), $this->hash(['foo' => 1, 'bar' => 2]));
    }

    public function testDictionariesWithSameValuesAndKeysInDifferentOrderHaveSameHashes()
    {
        $this->assertEquals($this->hash(['foo' => 1, 'bar' => 2]), $this->hash(['bar' => 2, 'foo' => 1]));
    }

    public function testDictionariesWithDifferentValuesHaveDifferentHashes()
    {
        $this->assertNotEquals($this->hash(['foo' => 1, 'bar' => 2]), $this->hash(['foo' => 2, 'bar' => 1]));
    }

    public function testJsonableObjectsWithSameValuesHaveSameHashes()
    {
        $obj1 = new class {
            public function toJson()
            {
                return '{"a":1,"b":2}';
            }
        };

        $obj2 = new class {
            public function toJson()
            {
                return '{"a":1,"b":2}';
            }
        };

        $this->assertEquals($this->hash($obj1), $this->hash($obj2));
    }

    public function testJsonableObjectsWithDifferentValuesHaveDifferentHashes()
    {
        $obj1 = new class {
            public function toJson()
            {
                return '{"a":1,"b":2}';
            }
        };

        $obj2 = new class {
            public function toJson()
            {
                return '{"a":2,"b":1}';
            }
        };

        $this->assertNotEquals($this->hash($obj1), $this->hash($obj2));
    }

    public function testArrayableObjectsWithSameValuesHaveSameHashes()
    {
        $obj1 = new class {
            public function toArray()
            {
                return ['a' => 1, 'b' => 2];
            }
        };

        $obj2 = new class {
            public function toArray()
            {
                return ['b' => 2, 'a' => 1];
            }
        };

        $this->assertEquals($this->hash($obj1), $this->hash($obj2));
    }

    public function testArrayableObjectsWithDifferentValuesHaveDifferentHashes()
    {
        $obj1 = new class {
            public function toArray()
            {
                return ['a' => 1, 'b' => 2];
            }
        };

        $obj2 = new class {
            public function toArray()
            {
                return ['a' => 2, 'b' => 1];
            }
        };

        $this->assertNotEquals($this->hash($obj1), $this->hash($obj2));
    }

    public function testSerializableObjectsWithSameValuesHaveSameHashes()
    {
        $obj1 = (object) ['a' => 1, 'b' => 2];

        $obj2 = (object) ['a' => 1, 'b' => 2];

        $this->assertEquals($this->hash($obj1), $this->hash($obj2));
    }

    public function testSerializableObjectsWithDifferentValuesHaveDifferentHashes()
    {
        $obj1 = (object) ['a' => 1, 'b' => 2];

        $obj2 = (object) ['a' => 2, 'b' => 1];

        $this->assertNotEquals($this->hash($obj1), $this->hash($obj2));
    }
}
