<?php

namespace Flat3\Lodata\Tests\Parser;

class LambdaTest extends ExpressionTest
{
    public function test_5b()
    {
        $this->assertLambda("airports/any(d:d/name eq 'hello')");
    }

    public function test_5d()
    {
        $this->assertLambda("airports/all(d:d/name eq 'hello')");
    }

    public function test_5e()
    {
        $this->assertLambda("da/all(d:d/name eq 'hello')");
    }

    public function test_6a()
    {
        $this->assertFilter("priorityC/any(a:a eq Priorities'high')");
    }

    public function test_6b()
    {
        $this->assertFilter("priorityC/all(a:a eq Priorities'high'");
    }

    public function test_6c()
    {
        $this->assertFilter("priorityC/any(a:a in (Priorities'high', Priorities'medium')");
    }
}
