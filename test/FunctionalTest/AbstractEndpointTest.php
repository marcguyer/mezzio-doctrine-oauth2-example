<?php

declare(strict_types=1);

namespace FunctionalTest;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\Constraint;

/**
 * Abstract to set up functional testing via endpoint provider config.
 */
abstract class AbstractEndpointTest extends AbstractFunctionalTest
{
    /**
     * Provider for testEndpoint() method.
     *
     * @see self::testEndpoint() for provider signature
     *
     * @return array
     */
    abstract public function endpointProvider(): array;

    /**
     * @param string $method
     * @param string $uri
     * @param array  $requestHeaders
     * @param array  $body
     * @param array  $queryParams
     *
     * @return ServerRequestInterface
     */
    protected function getRequest(
        string $method,
        string $uri,
        array $requestHeaders = [],
        array $body = [],
        array $queryParams = []
    ): ServerRequestInterface {
        $uri = new Uri($uri);

        if (null !== $body) {
            $bodyStream = fopen('php://memory', 'r+');
            fwrite($bodyStream, json_encode($body));
            $body = new Stream($bodyStream);
        }

        if (!empty($queryParams)) {
            $uri = $uri->withQuery(http_build_query($queryParams));
        }

        return new ServerRequest(
            [],
            [],
            $uri,
            $method,
            $body ?? 'php://input',
            $requestHeaders ?? []
        );
    }

    /**
     * @dataProvider endpointProvider
     *
     * @param ServerRequestInterface $request
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
     * @param Constraint[]      $responseConstraints
     * @param ResponseInterface $response
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
