<?php
declare(strict_types=1);

namespace AppFunctionalTest;

use Helmich\JsonAssert\Constraint\JsonValueMatchesMany;
use Helmich\Psr7Assert\Psr7Assertions;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @coversNothing
 */
abstract class AbstractFunctionalTest extends TestCase
{
    use Psr7Assertions;

    protected ContainerInterface $container;

    protected Application $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initContainer();
        $this->initApp();
        $this->initPipeline();
        $this->initRoutes();
    }

    protected function initContainer(): void
    {
        /** @psalm-suppress MixedAssignment */
        $this->container = require __DIR__ . '/../../config/container.php';
    }

    protected function initApp(): void
    {
        /** @psalm-suppress MixedAssignment */
        $this->app = $this->container->get(Application::class);
    }

    protected function initPipeline(): void
    {
        /** @var MiddlewareFactory $factory */
        $factory = $this->container->get(MiddlewareFactory::class);
        (require __DIR__ . '/../../config/pipeline.php')($this->app, $factory, $this->container);
    }

    protected function initRoutes(): void
    {
        /** @var MiddlewareFactory $factory */
        $factory = $this->container->get(MiddlewareFactory::class);
        (require __DIR__ . '/../../config/routes.php')($this->app, $factory, $this->container);
    }

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
