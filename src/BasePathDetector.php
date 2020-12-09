<?php

namespace Selective\BasePath;

/**
 * A URL base path detector.
 */
class BasePathDetector
{
    /**
     * @var array<mixed> The server data
     */
    private $server;

    /**
     * @var string The PHP_SAPI value
     */
    private $phpSapi;

    /**
     * The constructor.
     *
     * @param array<mixed> $server The SERVER data to use
     * @param string|null $phpSapi The PHP_SAPI value
     */
    public function __construct(array $server, string $phpSapi = null)
    {
        $this->server = $server;
        $this->phpSapi = $phpSapi ?? PHP_SAPI;
    }

    /**
     * Calculate the url base path.
     *
     * @return string The base path
     */
    public function getBasePath(): string
    {
        // For apache
        if ($this->phpSapi === 'apache2handler') {
            return $this->getBasePathFromApache($this->server);
        }

        // For built-in server
        if ($this->phpSapi === 'cli-server') {
            return $this->getBasePathFromBuiltIn($this->server);
        }

        return '';
    }

    /**
     * Return basePath for built-in server.
     *
     * @param array<mixed> $server The SERVER data to use
     *
     * @return string The base path
     */
    private function getBasePathFromBuiltIn(array $server): string
    {
        $scriptName = $server['SCRIPT_NAME'];
        $basePath = str_replace('\\', '/', dirname($scriptName));

        if (strlen($basePath) > 1) {
            return $basePath;
        }

        return '';
    }

    /**
     * Return basePath for apache server.
     *
     * @param array<mixed> $server The SERVER data to use
     *
     * @return string The base path
     */
    private function getBasePathFromApache(array $server): string
    {
        if (!isset($server['REQUEST_URI'])) {
            return '';
        }

        $scriptName = $server['SCRIPT_NAME'];

        $basePath = (string)parse_url($server['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = str_replace('\\', '/', dirname(dirname($scriptName)));

        if ($scriptName === '/') {
            return '';
        }

        $length = strlen($scriptName);
        if ($length > 0 && $scriptName !== '/') {
            $basePath = substr($basePath, 0, $length);
        }

        if (strlen($basePath) > 1) {
            return $basePath;
        }

        return '';
    }
}
