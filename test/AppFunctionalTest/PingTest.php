<?php

declare(strict_types=1);

namespace AppFunctionalTest;

use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Assert;

/**
 * Tests only PING.
 */
class PingTest extends AbstractFunctionalTest
{
    public function testPing(): void
    {
        $request = new ServerRequest(
            uri: '/api/ping',
            method: 'GET'
        );
        $timestamp = \time();
        $response = $this->app->handle($request);
        self::assertThat($response, self::isSuccess());
        self::assertThat($response, self::bodyMatchesJson([
            'ack' => Assert::greaterThanOrEqual($timestamp),
        ]));
    }
}
