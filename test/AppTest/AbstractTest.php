<?php
declare(strict_types=1);

namespace AppTest;

use Helmich\JsonAssert\Constraint\JsonValueMatchesMany;
use Helmich\Psr7Assert\Psr7Assertions;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
    use Psr7Assertions;

    /**
     * Override parent method's hard-coded regex
     */
    public static function bodyMatchesJson(array $constraints): Constraint
    {
        return Assert::logicalAnd(
            self::hasHeader(
                'content-type',
                Assert::matchesRegularExpression(
                    ',^application/(.+\+)?json(;.+)?$,'
                )
            ),
            self::bodyMatches(
                Assert::logicalAnd(
                    Assert::isJson(),
                    new JsonValueMatchesMany($constraints)
                )
            )
        );
    }
}
