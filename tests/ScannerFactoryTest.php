<?php
namespace Tests;

use Niisan\ClamAV\ScannerFactory;
use Niisan\ClamAV\Scanners\LocalScanner;
use Niisan\ClamAV\Scanners\RemoteScanner;

class ScannerFactoryTest extends TestCase
{

    /**
     * @test
     */
    public function getRemoteScanner()
    {
        $config = [
            'driver' => 'remote',
            'remote' => [
                'url' => 'example.com'
            ]
        ];

        $obj = ScannerFactory::create($config);
        $this->assertTrue($obj instanceof RemoteScanner);
    }

    /**
     * @test
     */
    public function getLocalScanner()
    {
        $config = [
            'driver' => 'local',
            'local' => [
                'path' => '/var/run/clamav/clamd.ctl'
            ]
        ];

        $obj = ScannerFactory::create($config);
        $this->assertTrue($obj instanceof LocalScanner);
    }
}