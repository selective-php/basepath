<?php

namespace Selective\BasePath\Test;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

/**
 * Test.
 */
class BasePathMiddlewareTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $app = AppFactory::create();

        $app->add(new BasePathMiddleware($app));

        $app->get('/', function ($request, $response) {
            return $response;
        });

        $app->run();

        $basePath = $app->getBasePath();

        static::assertSame('', $basePath);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testWithSubDirectory(): void
    {
        $app = AppFactory::create();

        $app->add(new BasePathMiddleware($app, 'apache2handler'));

        $app->get('/foo', function ($request, $response) {
            return $response;
        });

        $server = [
            'REQUEST_URI' => '/slim4-hello-world/',
            'SCRIPT_NAME' => '/slim4-hello-world/public/index.php',
        ];
        $app->run($this->createRequest('GET', '/slim4-hello-world/foo', $server));

        $basePath = $app->getBasePath();

        static::assertSame('/slim4-hello-world', $basePath);
    }

    /**
     * Create a server request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri The URI
     * @param array<mixed> $serverParams The server parameters
     *
     * @return ServerRequestInterface The request
     */
    private function createRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $factory = new ServerRequestFactory();

        return $factory->createServerRequest($method, $uri, $serverParams);
    }
}
