<?php
namespace Tests\Scanners;

use Niisan\ClamAV\Scanners\RemoteScanner;
use Tests\TestCase;

class RemoteScannerTest extends TestCase
{
    /**
     * @test
     */
    public function scanNormalFile()
    {
        $str = 'abcdefg12345678990';
        for ($i = 0; $i < 7; $i++) {
            $str .= $str;
        }
        $temp = tempnam(__DIR__, 'test');
        file_put_contents($temp, $str);
        $manager = new RemoteScanner(['host' => 'clamav']);
        $result = $manager->scan($temp);

        $this->assertTrue($result);
        unlink($temp);
    }

    /**
     * @test
     */
    public function scanErroFile()
    {
        $vi = 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR';
        $rus = '-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*';
        $temp = tempnam('/tmp', 'test');
        file_put_contents($temp, $vi.$rus);
        $manager = new RemoteScanner(['host' => 'clamav']);
        $result = $manager->scan($temp);

        $this->assertFalse($result);
        unlink($temp);
    }

    /**
     * @test
     */
    public function weSendPingAndGetPong()
    {
        $manager = new RemoteScanner(['host' => 'clamav']);
        $this->assertTrue($manager->ping());
    }
}
