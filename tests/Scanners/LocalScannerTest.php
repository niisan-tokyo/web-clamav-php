<?php
namespace Tests\Scanners;

use Niisan\ClamAV\Scanners\LocalScanner;
use Tests\TestCase;

class LocalScannerTest extends TestCase
{

    /**
     * @test
     */
    public function weSendPingAndGetPongWithUnixSocket()
    {
        $scanner = new LocalScanner(['path' => '/var/run/clamav/clamd.ctl']);
        $this->assertTrue($scanner->ping());
    }

    /**
     * @test
     */
    public function scanFile()
    {
        $str = 'abcdefg12345678990';
        for ($i = 0; $i < 7; $i++) {
            $str .= $str;
        }
        $temp = tempnam(__DIR__, 'test');
        file_put_contents($temp, $str);
        chmod($temp, '0644');
        $manager = new LocalScanner(['path' => '/var/run/clamav/clamd.ctl']);
        $result = $manager->scan($temp);

        $this->assertTrue($result);
        unlink($temp);
    }

    /**
     * @test
     */
    public function findVirus()
    {
        $vi = 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR';
        $rus = '-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*';
        $temp = tempnam('/tmp', 'test');
        file_put_contents($temp, $vi.$rus);
        chmod($temp, '0644');
        $manager = new LocalScanner(['path' => '/var/run/clamav/clamd.ctl']);
        $result = $manager->scan($temp);

        $this->assertFalse($result);
        unlink($temp);
    }
}