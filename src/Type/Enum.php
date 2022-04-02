<?php

declare(strict_types=1);

namespace Flat3\Lodata\Type;

use BackedEnum;
use Flat3\Lodata\EnumerationType;
use Flat3\Lodata\Exception\Protocol\BadRequestException;
use Flat3\Lodata\Helper\Constants;
use Flat3\Lodata\Helper\EnumMember;
use Flat3\Lodata\Helper\Identifier;
use Flat3\Lodata\Helper\JSON;
use Flat3\Lodata\Primitive;

/**
 * Enum
 * @package Flat3\Lodata\Type
 */
class Enum extends Primitive
{
    /** @var int $value */
    protected $value = 0;

    /** @var EnumerationType $type */
    protected $type;

    public function __construct(EnumerationType $type, $value)
    {
        $this->type = $type;
        parent::__construct($value);
    }

    public function toUrl(): string
    {
        if ($this->value === 0) {
            return Constants::null;
        }

        return $this->type->getIdentifier().String_::escape($this->toJson());
    }

    public function toJson(): ?string
    {
        if ($this->value === 0) {
            return null;
        }

        $result = [];

        foreach ($this->toFlags() as $flag) {
            $result[] = $flag->getName();
        }

        return join(',', $result);
    }

    public function toMixed(): int
    {
        return $this->value;
    }

    /**
     * Set the value of this enumeration
     * @param $value
     * @return $this
     * @throws \JsonException
     */
    public function set($value): self
    {
        if ($value === null) {
            $this->value = 0;

            return $this;
        }

        if (is_int($value)) {
            $this->value = $value;

            return $this;
        }

        if (is_numeric($value) && (int) $value === JSON::decode($value)) {
            $this->value = (int) $value;

            return $this;
        }

        if ($value instanceof BackedEnum) {
            $this->value = $value->value;

            return $this;
        }

        $flags = array_filter(array_map('trim', explode(',', (string) $value)));
        $valid = false;

        foreach ($this->type->getMembers() as $member) {
            if (in_array($member->getName(), $flags)) {
                $this->addFlag($member);
                $valid = true;
            }
        }

        if (false === $valid) {
            throw new BadRequestException(
                'invalid_flag',
                sprintf('The provided flag value "%s" was not valid for this type', $value)
            );
        }

        return $this;
    }

    /**
     * Add a flag to this enumeration
     * @param  EnumMember  $flag
     * @return $this
     */
    public function addFlag(EnumMember $flag): self
    {
        $this->value |= $flag->getValue();

        return $this;
    }

    /**
     * Drop a flag from this enumeration
     * @param  EnumMember  $flag
     * @return $this
     */
    public function dropFlag(EnumMember $flag): self
    {
        $this->value &= ~$flag->getValue();

        return $this;
    }

    /**
     * Check if this enumeration has the provided flag set
     * @param  EnumMember  $flag
     * @return bool
     */
    public function hasFlag(EnumMember $flag): bool
    {
        return ($flag->getValue() & $this->value) === $flag->getValue();
    }

    /**
     * Check that this enumeration has all the provided flags
     * @param  EnumMember[]  $flags
     * @return bool
     */
    public function hasFlags(array $flags): bool
    {
        $sum = 0;

        foreach ($flags as $flag) {
            $sum |= $flag->getValue();
        }

        return ($sum & $this->value) === $sum;
    }

    /**
     * Get the flags set on this enumeration
     * @return EnumMember[] Flags
     */
    public function toFlags(): array
    {
        $flags = [];

        foreach ($this->type->getMembers() as $member) {
            if ($this->hasFlag($member)) {
                $flags[] = $member;
            }
        }

        return $flags;
    }

    public function toValues(): array
    {
        return array_map(function (EnumMember $member) {
            return $member->getValue();
        }, $this->toFlags());
    }

    /**
     * Clear all the flags set on this enumeration
     * @return $this
     */
    public function clearFlags(): self
    {
        $this->value = 0;

        return $this;
    }

    public function getIdentifier(): Identifier
    {
        return $this->type->getIdentifier();
    }
}
