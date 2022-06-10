<?php

declare(strict_types=1);

namespace Flat3\Lodata\Drivers\SQL;

use Flat3\Lodata\Expression\Node;
use Flat3\Lodata\Expression\Node\Func;
use Flat3\Lodata\Expression\Node\Literal;
use Flat3\Lodata\Expression\Node\Operator\Lambda;
use Flat3\Lodata\Expression\Node\Operator\Navigation;
use Flat3\Lodata\Expression\Node\Property;
use Flat3\Lodata\Expression\Operator;
use Flat3\Lodata\Expression\Parser\Common;

trait SQLJoin
{

    /**
     * Generate join clauses for filter and search parameters
     * @return SQLJoinDefinition[]
     */
    protected function generateJoins(): array
    {
        /** @var SQLJoinDefinition[] $results */
        $results = [];

        $filter = $this->getFilter();

        if ($filter->hasValue()) {
            $parser = $this->getFilterParser();
            $parser->pushEntitySet($this);

            $expr = $filter->getExpression();
            $tree = $parser->generateTree($expr);

            $results = $this->findNavigationProperties($tree);
        }

        $results = $this->array_distinct($results, fn (SQLJoinDefinition $p) => $p->getAlias());

        return $results;
    }

    protected function array_distinct(array $src, callable $callable): array
    {
        $result = array_map($callable, $src);
        $unique = array_unique($result);
        return array_values(array_intersect_key($src, $unique));
    }

    /**
     *
     * @return SQLJoinDefinition[]
     */
    protected function findNavigationProperties(Node $node): array
    {
        /** @var SQLJoinDefinition[] $results */
        $results = [];

        switch (true) {
                // case $node instanceof Func:
                //     $this->functionExpression($node);
                //     break;

            case $node instanceof Navigation:
                $newJoinSQLDefinition = new SQLJoinDefinition();

                /** @var Navigation $node */
                /** @var NavigationProperty $navigationProperty */
                $navigationProperty = $node->getNavigationProperty()->getValue();
                /** @var NavigationBinding $navigationBinding */
                $navigationBinding = $this->getBindingByNavigationProperty($navigationProperty);
                /** @var SQLEntitySet $targetSet */
                $targetSet = $navigationBinding->getTarget();

                $constraints = $navigationProperty->getConstraints()->all();

                while ($constraints) {
                    $constraint = array_shift($constraints);
                    $newJoinSQLDefinition->setAlias("{$targetSet->getTable()}");
                    $newJoinSQLDefinition->setRelationProperty("{$targetSet->getTable()}.{$constraint->getReferencedProperty()->getName()}");
                    $newJoinSQLDefinition->setRelatedPoperty("{$this->getTable()}.{$constraint->getProperty()->getName()}");
                }

                $results[] = $newJoinSQLDefinition;
                break;

                // case $node instanceof Lambda:
                //     $this->lambdaExpression($node);
                //     break;

                // case $node instanceof Property:
                //     $this->propertyExpression($node);
                //     break;

                // case $node instanceof Literal:
                //     $this->literalExpression($node);
                //     break;

            case $node instanceof Operator:
                foreach ($node->getArguments() as $subNode) {
                    $results = array_merge($results, $this->findNavigationProperties($subNode));
                }

                $leftNode = $node->getLeftNode();
                $rightNode = $node->getRightNode();

                if ($leftNode) {
                    $results = array_merge($results, $this->findNavigationProperties($leftNode));
                }
                if ($rightNode) {
                    $results = array_merge($results, $this->findNavigationProperties($rightNode));
                }
                break;
        }

        return $results;
    }
}
