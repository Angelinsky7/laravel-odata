<?php

declare(strict_types=1);

namespace Flat3\Lodata\Transaction\Option;

use Flat3\Lodata\Controller\Transaction;
use Flat3\Lodata\Expression\Lexer;
use Flat3\Lodata\Transaction\NavigationRequest;
use Flat3\Lodata\Transaction\Option;

/**
 * Expand
 * @link https://docs.oasis-open.org/odata/odata/v4.01/odata-v4.01-part1-protocol.html#sec_SystemQueryOptionexpand
 * @package Flat3\Lodata\Transaction\Option
 */
class Expand extends Option
{
    public const param = 'expand';

    public function getNavigationTransaction(){
        $expanded = $this->getValue();

        $result = new Transaction();

        if (!$expanded) {
            return null;
        }

        $lexer = new Lexer($expanded);

        $path = $lexer->identifier();

        $navigationRequest = new NavigationRequest();
        $navigationRequest->setPath($path);
        $queryParameters = $lexer->with(function (Lexer $lexer) {
            return $lexer->matchingParenthesis();
        });
        if ($queryParameters) {
            $navigationRequest->setQueryString($queryParameters);
        }

        $result->initialize($navigationRequest);

        return $result;
    }
    
}
