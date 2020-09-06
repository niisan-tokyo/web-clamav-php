<?php
namespace Niisan\ClamAV;

use Niisan\ClamAV\Scanners\LocalScanner;
use Niisan\ClamAV\Scanners\RemoteScanner;

class ScannerFactory
{
    public static function create(array $config): Scanner
    {
        if (!isset($config['driver'])) {
            throw new \RuntimeException('Set config: driver');
        }

        if ($config['driver'] === 'remote') {
            return self::createRemoteScanner($config);
        }

        if ($config['driver'] === 'local') {
            return self::createLocalScanner($config);
        }

        throw new \RuntimeException('The specified driver is not valid: ' . $config['driver']);
    }

    private static function createRemoteScanner(array $config): RemoteScanner
    {
        $config = $config['remote'] ?? $config;
        return new RemoteScanner($config);
    }

    private static function createLocalScanner(array $config): LocalScanner
    {
        $config = $config['local'] ?? $config;
        return new LocalScanner($config);
    }
}