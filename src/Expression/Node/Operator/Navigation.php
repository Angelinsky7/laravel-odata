<?php

declare(strict_types=1);

namespace Flat3\Lodata\Expression\Node\Operator;

use Flat3\Lodata\Expression\Node\Property;
use Flat3\Lodata\Expression\Node\Property\Navigation as PropertyNavigation;
use Flat3\Lodata\Expression\Operator;

/**
 * Lambda
 * @package Flat3\Lodata\Expression\Node\Operator
 */
class Navigation extends Operator
{
    public const precedence = PHP_INT_MAX;

    /**
     * @var PropertyNavigation $navigationProperty
     */
    protected $navigationProperty;

    /**
     * @var Property $navigationProperty
     */
    protected $property;

    /**
     * Get the navigation property
     * @return PropertyNavigation
     */
    public function getNavigationProperty(): PropertyNavigation
    {
        return $this->navigationProperty;
    }

    /**
     * Set the navigation property
     * @param  PropertyNavigation  $property
     * @return $this
     */
    public function setNavigationProperty(PropertyNavigation $property): self
    {
        $this->navigationProperty = $property;

        return $this;
    }

     /**
     * Get the property
     * @return Property
     */
    public function getProperty(): Property
    {
        return $this->property;
    }

    /**
     * Set the property
     * @param  Property $property
     * @return $this
     */
    public function setProperty(Property $property): self
    {
        $this->property = $property;

        return $this;
    }


}
