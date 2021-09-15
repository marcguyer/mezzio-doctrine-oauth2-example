<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\PingHandler;
use Helmich\Psr7Assert\Psr7Assertions;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Assert;

class PingHandlerTest extends \AppTest\AbstractTest
{
    use Psr7Assertions;

    public function testPingResponse()
    {
        $pingHandler = new PingHandler();
        $request = new ServerRequest(
            uri: '/api/ping',
            method: 'GET'
        );
        $timestamp = \time();
        $response = $pingHandler->handle($request);
        self::assertThat($response, self::isSuccess());
        self::assertThat($response, self::bodyMatchesJson([
            'ack' => Assert::greaterThanOrEqual($timestamp),
        ]));
    }
}
