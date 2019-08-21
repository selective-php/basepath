<?php

namespace Selective\BasePath\Test;

use PHPUnit\Framework\TestCase;
use Selective\BasePath\BasePathDetector;

/**
 * Test.
 */
class ApacheTest extends TestCase
{
    /**
     * @var array
     */
    private $server;

    /**
     * Set Up.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_SCHEME' => 'http',
            'HTTP_HOST' => 'localhost',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'SCRIPT_NAME' => '',
            'REQUEST_TIME_FLOAT' => microtime(true),
            'REQUEST_TIME' => microtime(),
        ];
    }

    /**
     * Create instance.
     *
     * @return BasePathDetector The detector
     */
    private function createInstance(): BasePathDetector
    {
        return new BasePathDetector($this->server, 'apache2handler');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $detector = $this->createInstance();
        $basePath = $detector->getBasePath();

        static::assertSame('', $basePath);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testUnknownServer(): void
    {
        $detector = new BasePathDetector($this->server, '');
        $basePath = $detector->getBasePath();

        static::assertSame('', $basePath);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testSubdirectory(): void
    {
        $this->server['REQUEST_URI'] = '/public';

        $detector = $this->createInstance();
        $basePath = $detector->getBasePath();

        static::assertSame('/public', $basePath);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testWithoutRequestUri(): void
    {
        unset($this->server['REQUEST_URI']);

        $detector = $this->createInstance();
        $basePath = $detector->getBasePath();

        static::assertSame('', $basePath);
    }
}
