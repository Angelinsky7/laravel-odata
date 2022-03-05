<?php

declare(strict_types=1);

namespace Flat3\Lodata\Attributes;

use Attribute;
use Flat3\Lodata\Type;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class LodataEnum extends LodataProperty
{
    protected string $enum;
    protected ?bool $isFlags = null;

    public function __construct(string $name, string $enum, ?string $source = null, ?bool $isFlags = null)
    {
        parent::__construct($name, $source);
        $this->enum = $enum;
        if (null !== $isFlags) {
            $this->isFlags = $isFlags;
        }
    }

    public function getEnum(): string
    {
        return $this->enum;
    }

    public function getIsFlags(): ?bool
    {
        return $this->isFlags;
    }

    public function getType(): Type
    {
        return Type::enum($this->getName());
    }
}