<?php

namespace BYanelli\Memoizer\Tests;

use BYanelli\Memoizer\Widget;
use PHPUnit\Framework\TestCase;

class WidgetTest extends TestCase
{
    public function testSomething()
    {
        $widget = new Widget();
        $this->assertTrue($widget->doSomething());
    }
}
