<?php
namespace Niisan\ClamAV;

class TcpSocket
{
    private $url;
    private $port = 3310;
    private $socket;

    public function __construct(string $url, ?int $port)
    {
        $this->url = $url;
        $this->port = $port ?? $this->port;
    }

    public function send($commands)
    {
        if (is_string($commands)) {
            $commands = [$commands];
        }
        $socket = $this->getSocket();
        foreach ($commands as $command) {
            stream_socket_sendto($socket, $command);
        }
        $ret = stream_socket_recvfrom($socket, 65536);
        fclose($socket);
        return $ret;
    }

    private function getSocket()
    {
        $address = sprintf("tcp://%s:%s", $this->url, $this->port);
        $socket = stream_socket_client($address);
        if (! $socket) {
            throw new \RuntimeException('Connection failed: ' . $address);
        }

        return $socket;
    }
}