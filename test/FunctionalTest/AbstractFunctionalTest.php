<?php

declare(strict_types=1);

namespace FunctionalTest;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;
use Helmich\Psr7Assert\Psr7Assertions;

/**
 * {@inheritdoc}
 */
abstract class AbstractFunctionalTest extends TestCase
{
    use Psr7Assertions;

    /** @var ContainerInterface */
    protected static $container;

    /** @var Application */
    protected static $app;

    public static function setUpBeforeClass(): void
    {
        static::initContainer();
        static::initApp();
        static::initPipeline();
        static::initRoutes();
    }

    public static function tearDownAfterClass(): void
    {
        static::$container = null;
        static::$app = null;
    }

    /**
     * Initialize new container instance.
     */
    protected static function initContainer(): void
    {
        static::$container = require 'config/container.php';
    }

    /**
     * Initialize app.
     */
    protected static function initApp(): void
    {
        static::$app = static::$container->get(Application::class);
    }

    /**
     * Initialize pipeline.
     */
    protected static function initPipeline(): void
    {
        (require 'config/pipeline.php')(
            static::$app,
            static::$container->get(MiddlewareFactory::class),
            static::$container
        );
    }

    /**
     * Initialize routes.
     */
    protected static function initRoutes(): void
    {
        (require 'config/routes.php')(
            static::$app,
            static::$container->get(MiddlewareFactory::class),
            static::$container
        );
    }
}
