<?php

declare(strict_types=1);

namespace FunctionalTest;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\Constraint;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Abstract to set up functional testing via endpoint provider config.
 */
abstract class AbstractEndpointTest extends AbstractFunctionalTest
{
    /**
     * Provider for testEndpoint() method.
     *
     * @see self::testEndpoint() for provider signature
     */
    abstract public function endpointProvider(): array;

    protected function getRequest(
        string $method,
        string $uri,
        array $requestHeaders = [],
        array $body = [],
        array $queryParams = []
    ): ServerRequestInterface {
        if ($body) {
            $bodyStream = fopen('php://memory', 'r+');
            fwrite($bodyStream, json_encode($body));
            $body = new Stream($bodyStream);
        }

        return new ServerRequest(
            [],
            [],
            $uri,
            $method,
            $body ?? 'php://input',
            $requestHeaders ?? [],
            [],
            $queryParams
        );
    }

    /**
     * @dataProvider endpointProvider
     *
     * @param Constraint[] $responseConstraints
     */
    public function testEndpoint(
        ServerRequestInterface $request,
        array $responseConstraints = []
    ): void {
        $response = static::$app->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseConstraints($responseConstraints, $response);
    }

    /**
     * @param Constraint[] $responseConstraints
     */
    protected function assertResponseConstraints(
        array $responseConstraints,
        ResponseInterface $response
    ): void {
        if (empty($responseConstraints)) {
            $responseConstraints[] = self::isSuccess();
        }
        foreach ($responseConstraints as $msg => $constraint) {
            $this->assertThat(
                $response,
                $constraint,
                is_string($msg) ? $msg : ''
            );
        }
    }
}
