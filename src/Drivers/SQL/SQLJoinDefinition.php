<?php

declare(strict_types=1);

namespace Flat3\Lodata\Drivers\SQL;

class SQLJoinDefinition
{

    /** @var string $alias */
    protected string $alias = '';

    /** @var string $relationProperty */
    protected string $relationProperty = '';

    /** @var string $relatedPoperty */
    protected string $relatedPoperty = '';

    /**
     * Get alias
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Set alias
     * @param string $alias alias
     * @return void
     */
    public function setAlias(string $value): void
    {
        $this->alias = $value;
    }

    /**
     * Get relationProperty
     * @return string
     */
    public function getRelationProperty(): string
    {
        return $this->relationProperty;
    }

    /**
     * Set relationProperty
     * @param string $relationProperty relationProperty
     * @return void
     */
    public function setRelationProperty(string $value): void
    {
        $this->relationProperty = $value;
    }

    /**
     * Get relatedPoperty
     * @return string
     */
    public function getRelatedPoperty(): string
    {
        return $this->relatedPoperty;
    }

    /**
     * Set relatedPoperty
     * @param string $relatedPoperty relatedPoperty
     * @return void
     */
    public function setRelatedPoperty(string $value): void
    {
        $this->relatedPoperty = $value;
    }
}
