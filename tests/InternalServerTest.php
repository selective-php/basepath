<?php

namespace Selective\BasePath\Test;

use PHPUnit\Framework\TestCase;
use Selective\BasePath\BasePathDetector;

/**
 * Test.
 */
class InternalServerTest extends TestCase
{
    /**
     * @var array<mixed>
     */
    private $server;

    /**
     * Set Up.
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
        return new BasePathDetector($this->server, 'cli-server');
    }

    /**
     * Test.
     */
    public function testDefault(): void
    {
        $detector = $this->createInstance();
        $basePath = $detector->getBasePath();

        static::assertSame('', $basePath);

        $this->server['SCRIPT_NAME'] = '/index.php';
        $detector = $this->createInstance();
        $basePath = $detector->getBasePath();

        static::assertSame('', $basePath);
    }

    /**
     * Test.
     */
    public function testSubdirectory(): void
    {
        $this->server['SCRIPT_NAME'] = '/public/index.php';

        $detector = $this->createInstance();
        $basePath = $detector->getBasePath();

        static::assertSame('/public', $basePath);
    }
}
