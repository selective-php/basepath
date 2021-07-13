<?php

namespace Selective\BasePath\Test;

use PHPUnit\Framework\TestCase;
use Selective\BasePath\BasePathDetector;

/**
 * Test.
 */
class NonInternalTest extends TestCase {

    /**
     * @var array<mixed> The server data array contains multiple data types
     */
    private $server;

    /**
     * 
     * @var array<string> non-exhaustive list of possible values for PHP_SAPI
     */
    private $sapis = ['apache2handler', 'cgi', 'cgi-fcgi', 'fpm-fcgi', 'litespeed'];

    /**
     * Set Up.
     */
    protected function setUp(): void {
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
    private function createInstance(?string $sapi): BasePathDetector {
        return new BasePathDetector($this->server, $sapi);
    }

    /**
     * Test.
     */
    public function testDefault(): void {
        foreach ($this->sapis as $sapi) {
            $detector = $this->createInstance($sapi);
            $basePath = $detector->getBasePath();

            static::assertSame('', $basePath);
        }
    }

    /**
     * Test.
     */
    public function testUnknownServer(): void {
        $detector = new BasePathDetector($this->server, '');
        $basePath = $detector->getBasePath();

        static::assertSame('', $basePath);
    }

    /**
     * Test.
     */
    public function testSubdirectory(): void {
        $this->server['REQUEST_URI'] = '/public';

        foreach ($this->sapis as $sapi) {
            $detector = $this->createInstance($sapi);
            $basePath = $detector->getBasePath();

            static::assertSame('/public', $basePath);
        }
    }

    /**
     * Test.
     */
    public function testWithoutRequestUri(): void {
        unset($this->server['REQUEST_URI']);
        foreach ($this->sapis as $sapi) {
            $detector = $this->createInstance($sapi);
            $basePath = $detector->getBasePath();

            static::assertSame('', $basePath);
        }
    }

}
