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
        // The built-in server
        if ($this->phpSapi === 'cli-server') {
            return $this->getBasePathByScriptName($this->server);
        }

        return $this->getBasePathByRequestUri($this->server);
    }

    /**
     * Return basePath for built-in server.
     *
     * @param array<mixed> $server The SERVER data to use
     *
     * @return string The base path
     */
    private function getBasePathByScriptName(array $server): string
    {
        $scriptName = (string)$server['SCRIPT_NAME'];
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
    private function getBasePathByRequestUri(array $server): string
    {
        if (!isset($server['REQUEST_URI'])) {
            return '';
        }

        $scriptName = $server['SCRIPT_NAME'];

        $basePath = (string)parse_url($server['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = str_replace('\\', '/', dirname($scriptName, 2));

        if ($scriptName === '/') {
            return '';
        }

        $length = strlen($scriptName);
        if ($length > 0) {
            $basePath = substr($basePath, 0, $length);
        }

        if (strlen($basePath) > 1) {
            return $basePath;
        }

        return '';
    }
}
