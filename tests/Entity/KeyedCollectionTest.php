<?php

declare(strict_types=1);

namespace Flat3\Lodata\Tests\Entity;

use Flat3\Lodata\Tests\Drivers\WithKeyedCollectionDriver;

class KeyedCollectionTest extends EntityTest
{
    use WithKeyedCollectionDriver;

    public function test_read_alternative_key()
    {
    }
}