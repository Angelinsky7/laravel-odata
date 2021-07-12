<?php

namespace Flat3\Lodata\Drivers;

use Flat3\Lodata\DeclaredProperty;
use Flat3\Lodata\Entity;
use Flat3\Lodata\EntitySet;
use Flat3\Lodata\Exception\Protocol\NotImplementedException;
use Flat3\Lodata\Expression\Event;
use Flat3\Lodata\Expression\Node;
use Flat3\Lodata\Expression\Node\Literal;
use Flat3\Lodata\Expression\Node\Operator;
use Flat3\Lodata\Expression\Parser\Filter as FilterParser;
use Flat3\Lodata\Expression\Parser\Search as SearchParser;
use Flat3\Lodata\Helper\PropertyValue;
use Flat3\Lodata\Interfaces\EntitySet\CountInterface;
use Flat3\Lodata\Interfaces\EntitySet\CreateInterface;
use Flat3\Lodata\Interfaces\EntitySet\DeleteInterface;
use Flat3\Lodata\Interfaces\EntitySet\FilterInterface;
use Flat3\Lodata\Interfaces\EntitySet\OrderByInterface;
use Flat3\Lodata\Interfaces\EntitySet\PaginationInterface;
use Flat3\Lodata\Interfaces\EntitySet\QueryInterface;
use Flat3\Lodata\Interfaces\EntitySet\ReadInterface;
use Flat3\Lodata\Interfaces\EntitySet\SearchInterface;
use Flat3\Lodata\Interfaces\EntitySet\UpdateInterface;
use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class CollectionEntitySet
 * @package Flat3\Lodata\Drivers
 */
class CollectionEntitySet extends EntitySet implements CountInterface, CreateInterface, DeleteInterface, QueryInterface, ReadInterface, UpdateInterface, PaginationInterface, OrderByInterface, SearchInterface, FilterInterface
{
    /** @var Collection $collection */
    protected $collection;

    /**
     * Set the collection used by this entity set
     * @param  Collection  $collection  Collection
     * @return $this
     */
    public function setCollection(Collection $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get the collection used by this entity set
     * @return Collection Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function query(): Generator
    {
        $collection = $this->collection;

        if ($this->getFilter()->hasValue()) {
            $parser = new FilterParser($this->getTransaction());
            $parser->pushEntitySet($this);

            $tree = $parser->generateTree($this->getFilter()->getValue());

            $collection = $collection->filter(function ($item) use ($tree) {
                $result = $tree->eval($this->newEntity()->fromArray($item));
                return $result !== null && !!$result->get();
            });
        }

        if ($this->getSearch()->hasValue()) {
            $search = $this->getSearch();

            $parser = new SearchParser();
            $parser->pushEntitySet($this);

            $tree = $parser->generateTree($search->getValue());

            $collection = $collection->filter(function ($item) use ($tree) {
                $eval = function (Node $node) use (&$eval, $item) {
                    $left = !$node->getLeftNode() ?: $eval($node->getLeftNode());
                    $right = !$node->getRightNode() ?: $eval($node->getRightNode());

                    switch (true) {
                        case $node instanceof Literal\String_:
                            /** @var DeclaredProperty[] $props */
                            $props = $this->getType()->getDeclaredProperties()->filter(function ($property) {
                                return $property->isSearchable();
                            });

                            foreach ($props as $prop) {
                                if (Str::contains($item[$prop->getName()], $node->getValue())) {
                                    return true;
                                }
                            }

                            return false;

                        case $node instanceof Operator\Comparison\And_:
                            return $left && $right;

                        case $node instanceof Operator\Comparison\Or_:
                            return $left || $right;

                        case $node instanceof Operator\Comparison\Not_:
                            return !$left;
                    }

                    throw new NotImplementedException();
                };

                return !!$eval($tree);
            });
        }

        if ($this->getOrderBy()->hasValue()) {
            $collection = $collection->map(function ($item, $key) {
                return array_merge(['__id' => $key], $item);
            })
                ->sortBy($this->getOrderBy()->getSortOrders())
                ->keyBy('__id');
        }

        if ($this->getSkip()->hasValue()) {
            $collection = $collection->skip($this->getSkip()->getValue());
        }

        if ($this->getTop()->hasValue()) {
            $collection = $collection->slice(0, $this->getTop()->getValue());
        }

        foreach ($collection->all() as $key => $item) {
            $entity = $this->newEntity();
            $entity->setEntityId($key);
            $entity->fromArray($item);

            yield $entity;
        }
    }

    public function create(): Entity
    {
        $entity = $this->newEntity();
        $body = $this->transaction->getBody();
        $entity->fromArray($body);
        $entityId = $entity->getEntityId();

        if ($entityId) {
            $key = $entityId->getPrimitiveValue()->get();
            $this->collection[$key] = $entity->toArray();
        } else {
            $this->collection[] = $entity->toArray();
            $entity->setEntityId($this->collection->count() - 1);
        }

        return $entity;
    }

    public function delete(PropertyValue $key): void
    {
        $this->collection->forget($key->getPrimitiveValue()->get());
    }

    public function read(PropertyValue $key): ?Entity
    {
        $item = $this->collection->get($key->getPrimitiveValue()->get());

        if ($item === null) {
            return null;
        }

        $entity = $this->newEntity();
        $entity['id'] = $key->getPrimitiveValue()->get();
        $entity->fromArray($item);

        return $entity;
    }

    public function update(PropertyValue $key): Entity
    {
        $entity = $this->read($key);
        $body = $this->transaction->getBody();
        $entity->fromArray($body);
        $item = $entity->toArray();
        unset($item['id']);

        $this->collection[$key->getPrimitiveValue()->get()] = $item;

        return $this->read($key);
    }

    public function search(Event $event): ?bool
    {
        return true;
    }

    public function filter(Event $event): ?bool
    {
        return true;
    }
}