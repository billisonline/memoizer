<?php

namespace BYanelli\Memoizer\Tests\Library;

class MemoizableFunction
{
    protected $numberOfTimesCalled = 0;

    public function __invoke()
    {
        return $this->numberOfTimesCalled++;
    }

    /**
     * @return int
     */
    public function getNumberOfTimesCalled(): int
    {
        return $this->numberOfTimesCalled;
    }
}