<?php

declare(strict_types=1);

namespace Flat3\Lodata\Type;

use Flat3\Lodata\Expression\Lexer;
use Flat3\Lodata\Helper\Constants;
use Flat3\Lodata\Primitive;

/**
 * String
 * @package Flat3\Lodata\Type
 */
class String_ extends Primitive
{
    const identifier = 'Edm.String';

    const openApiSchema = [
        'type' => Constants::oapiString,
    ];

    /** @var ?string $value */
    protected $value;

    public function toUrl(): string
    {
        if (null === $this->value) {
            return Constants::null;
        }

        return self::escape($this->value);
    }

    /**
     * Escape quotes in a string when using URLs
     * @param  string  $value
     * @return string URL Encoded value
     */
    public static function escape(string $value): string
    {
        return sprintf("'%s'", str_replace("'", "''", $value));
    }

    public function set($value): self
    {
        $this->value = null === $value ? null : (string) $value;

        return $this;
    }

    public function get(): ?string
    {
        return parent::get();
    }

    public function toJson(): ?string
    {
        return $this->value;
    }

    public function toMixed(): ?string
    {
        return $this->value;
    }

    public static function fromLexer(Lexer $lexer): Primitive
    {
        /** @phpstan-ignore-next-line */
        return new static($lexer->quotedString());
    }
}
