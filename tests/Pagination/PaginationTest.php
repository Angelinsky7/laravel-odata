<?php

declare(strict_types=1);

namespace Flat3\Lodata\Tests\Pagination;

use Flat3\Lodata\Facades\Lodata;
use Flat3\Lodata\Interfaces\EntitySet\PaginationInterface;
use Flat3\Lodata\Tests\Helpers\Request;
use Flat3\Lodata\Tests\TestCase;

abstract class PaginationTest extends TestCase
{
    public function test_top()
    {
        $this->assertJsonResponseSnapshot(
            (new Request)
                ->top('2')
                ->path($this->entitySetPath)
        );
    }

    public function test_skip()
    {
        if (!Lodata::getEntitySet('passengers') instanceof PaginationInterface) {
            return;
        }

        $this->assertJsonResponseSnapshot(
            (new Request)
                ->top('2')
                ->skip('2')
                ->path($this->entitySetPath)
        );
    }

    public function test_sequence()
    {
        $this->assertPaginationSequence(
            (new Request)
                ->top('2')
                ->path($this->entitySetPath)
        );
    }

    public function test_skip_invalid_type()
    {
        $this->assertBadRequest(
            (new Request)
                ->path($this->entitySetPath)
                ->skip('xyz')
        );
    }

    public function test_skip_invalid_negative()
    {
        $this->assertBadRequest(
            (new Request)
                ->path($this->entitySetPath)
                ->skip('-2')
        );
    }

    public function test_top_invalid_type()
    {
        $this->assertBadRequest(
            (new Request)
                ->path($this->entitySetPath)
                ->top('xyz')
        );
    }

    public function test_top_invalid_negative()
    {
        $this->assertBadRequest(
            (new Request)
                ->path($this->entitySetPath)
                ->top('-2')
        );
    }
}