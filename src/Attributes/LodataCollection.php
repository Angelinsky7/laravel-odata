<?php

declare(strict_types=1);

namespace Flat3\Lodata\Attributes;

use Attribute;
use Flat3\Lodata\EnumerationType;
use Flat3\Lodata\Facades\Lodata;
use Flat3\Lodata\Primitive;
use Flat3\Lodata\PrimitiveType;
use Flat3\Lodata\Type;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class LodataCollection extends LodataProperty
{
    /** @var string|Type $underlyingType */
    protected ?string $underlyingType = null;

    public function __construct(string $name, ?string $source = null, ?string $underlyingType = null)
    {
        parent::__construct($name, $source);

        $this->underlyingType = $underlyingType;
    }

    public function getType(): Type
    {
        return Type::collection();
    }

    public function hasUnderlyingType(): bool
    {
        return null !== $this->underlyingType;
    }

    public function getUnderlyingType(): Type
    {
        if (!$this->underlyingType) {
            return Type::untyped();
        }

        if (is_a($this->underlyingType, Primitive::class, true)) {
            return new PrimitiveType($this->underlyingType);
        }

        $type = Lodata::getTypeDefinition($this->underlyingType);

        if (!$type && EnumerationType::isEnum($this->underlyingType)) {
            $type = EnumerationType::discover($this->underlyingType);
        }

        return $type;
    }
}