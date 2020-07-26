<?php
namespace Niisan\ClamAV;

class Manager
{

    private $socket;
    private $message = true;

    public function __construct(array $option)
    {
        if (empty($option['url'])) {
            throw new \RuntimeException('ClamAV server host is not input.');
        }

        $this->socket = new TcpSocket($option['url'], $option['port'] ?? null);
    }

    public function ping(): bool
    {
        $this->message = $this->socket->send('PING');
        if ($this->checkMessage($this->message, 'PONG')) {
            throw new \RuntimeException("Not Connected to ClamAV server: $data has returned");
        }

        return true;
    }

    public function scan(string $path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("such file is not found: $path");
        }

        $this->message = $this->socket->send($this->splitFile($path));

        if ($this->checkMessage($this->message, 'OK')) {
            return true;
        }

        return false;
    }

    public function getMessage()
    {
        return trim($this->message);
    }

    private function checkMessage(string $message, string $check)
    {
        return trim(substr(strrchr($message, ":"), 1)) === $check;
    }

    private function splitFile($path)
    {
        $fp = fopen($path, 'rb');
        yield "zINSTREAM\0";

        while (! feof($fp)) {
            $str = fread($fp, 1024);
            yield pack('N', strlen($str)).$str;
        }

        yield pack('N', 0);
    }
}