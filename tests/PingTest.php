<?php

namespace Tests;

use Niisan\ClamAV\Manager;

class PingTest extends TestCase
{

    /**
     * @test
     */
    public function weSendPingAndGetPong()
    {
        $manager = new Manager(['url' => 'clamav']);
        $manager->ping();
        $this->assertEquals('PONG', $manager->getMessage());
    }
}