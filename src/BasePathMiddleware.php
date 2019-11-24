<?php

namespace Selective\BasePath;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

/**
 * Slim 4 Base path middleware.
 */
final class BasePathMiddleware implements MiddlewareInterface
{
    /**
     * @var App The slim app
     */
    private $app;

    /**
     * @var string|null
     */
    private $phpSapi;

    /**
     * The constructor.
     *
     * @param App $app The slim app
     * @param string|null $phpSapi The PHP_SAPI value
     */
    public function __construct(App $app, string $phpSapi = null)
    {
        $this->app = $app;
        $this->phpSapi = $phpSapi;
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $detector = new BasePathDetector($request->getServerParams(), $this->phpSapi);

        $this->app->setBasePath($detector->getBasePath());

        return $handler->handle($request);
    }
}
