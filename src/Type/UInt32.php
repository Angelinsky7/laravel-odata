<?php

declare(strict_types=1);

namespace Flat3\Lodata\Type;

use Flat3\Lodata\Helper\Constants;

/**
 * UInt32
 * @package Flat3\Lodata\Type
 */
class UInt32 extends Int32
{
    const identifier = 'UInt32';

    const underlyingType = Int32::class;

    const openApiSchema = [
        'type' => Constants::oapiInteger,
        'format' => 'int32',
        'minimum' => 0,
        'maximum' => (2 ** 31) - 1,
    ];

    public const format = 'L';
}
