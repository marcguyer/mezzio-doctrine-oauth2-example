<?php

declare(strict_types=1);

namespace FunctionalTest;

use PHPUnit\Framework\Assert;

/**
 * Tests only PING
 */
class PingTest extends AbstractEndpointTest
{
    /**
     * @return array
     */
    public function endpointProvider(): array
    {
        return [
            // GET /api/ping
            'api.ping.get' => [
                $this->getRequest('GET', '/api/ping'),
                [
                    self::isSuccess(),
                    self::bodyMatchesJson([
                        'ack' => Assert::greaterThanOrEqual(time())
                    ])
                ]
            ],
        ];
    }
}
