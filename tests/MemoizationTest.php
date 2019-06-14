<?php

namespace BYanelli\Memoizer\Tests;

use BYanelli\Memoizer\Memoizes;
use BYanelli\Memoizer\Tests\Library\MemoizableFunction;
use PHPUnit\Framework\TestCase;

class MemoizationTest extends TestCase
{
    public function testNonMemoizedFunctionCalledMoreThanOnce()
    {
        $memoizable = new MemoizableFunction;

        $obj = new class ($memoizable) {
            /**
             * @var callable
             */
            private $callable;

            public function __construct(callable $callable)
            {
                $this->callable = $callable;
            }

            public function nonMemoizedMethod()
            {
                return call_user_func($this->callable);
            }
        };

        $this->assertEquals(0, $memoizable->getNumberOfTimesCalled());

        $obj->nonMemoizedMethod();
        $obj->nonMemoizedMethod();

        $this->assertEquals(2, $memoizable->getNumberOfTimesCalled());
    }

    public function testMemoizedFunctionOnlyCalledOnce()
    {
        $memoizable = new MemoizableFunction;

        $obj = new class ($memoizable) {
            use Memoizes;

            /**
             * @var callable
             */
            private $callable;

            public function __construct(callable $callable)
            {
                $this->callable = $callable;
            }

            public function memoizedMethod()
            {
                return $this->memoize([], $this->callable);
            }
        };

        $this->assertEquals(0, $memoizable->getNumberOfTimesCalled());

        $obj->memoizedMethod();
        $obj->memoizedMethod();

        $this->assertEquals(1, $memoizable->getNumberOfTimesCalled());
    }

    public function testMemoizedFunctionsInDifferentMethodsCalledOnceEach()
    {
        $memoizable1 = new MemoizableFunction;
        $memoizable2 = new MemoizableFunction;

        $obj = new class ($memoizable1, $memoizable2) {
            use Memoizes;

            /**
             * @var callable
             */
            private $callable1;

            /**
             * @var callable
             */
            private $callable2;

            public function __construct(callable $callable1, callable $callable2)
            {
                $this->callable1 = $callable1;
                $this->callable2 = $callable2;
            }

            public function memoizedMethod1()
            {
                return $this->memoize([], $this->callable1);
            }

            public function memoizedMethod2()
            {
                return $this->memoize([], $this->callable2);
            }
        };

        $obj->memoizedMethod1();
        $obj->memoizedMethod1();

        $this->assertEquals(1, $memoizable1->getNumberOfTimesCalled());
        $this->assertEquals(0, $memoizable2->getNumberOfTimesCalled());

        $obj->memoizedMethod2();
        $obj->memoizedMethod2();

        $this->assertEquals(1, $memoizable1->getNumberOfTimesCalled());
        $this->assertEquals(1, $memoizable2->getNumberOfTimesCalled());
    }

    public function testMemoizedFunctionWithDependenciesCalledOncePerUniqueDependency()
    {
        $memoizable = new MemoizableFunction;

        $obj = new class ($memoizable) {
            use Memoizes;

            /**
             * @var callable
             */
            private $callable;

            public function __construct(callable $callable)
            {
                $this->callable = $callable;
            }

            public function memoizedMethod(array $dependencies)
            {
                return $this->memoize($dependencies, $this->callable);
            }
        };

        $this->assertEquals(0, $memoizable->getNumberOfTimesCalled());

        $obj->memoizedMethod(['foo', 'bar']);
        $obj->memoizedMethod(['foo', 'bar']);

        $this->assertEquals(1, $memoizable->getNumberOfTimesCalled());

        $obj->memoizedMethod(['bar', 'baz']);
        $obj->memoizedMethod(['bar', 'baz']);

        $this->assertEquals(2, $memoizable->getNumberOfTimesCalled());
    }
}