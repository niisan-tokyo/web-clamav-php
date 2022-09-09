<?php
namespace Niisan\ClamAV\Scanners;

use Generator;
use Niisan\ClamAV\Scanner;

class RemoteScanner implements Scanner
{

    private $port = 3310;
    private $host;

    public function __construct(array $option)
    {
        if (empty($option['host'])) {
            throw new \RuntimeException('ClamAV server host is not input.');
        }

        $this->host = $option['host'];
        $this->port = $option['port'] ?? $this->port;
    }

    /**
     * @inheritDoc
     */
    public function ping(): bool
    {
        //$this->message = $this->socket->send('PING');
        $message = $this->execWithSocket(function ($socket) {
            stream_socket_sendto($socket, 'PING');
            return stream_socket_recvfrom($socket, 65536);
        });
        if (! $this->checkMessage($message, 'PONG')) {
            throw new \RuntimeException("Not Connected to ClamAV server: $message has returned");
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function scan(string $file_path): bool
    {
        if (!file_exists($file_path)) {
            throw new \RuntimeException("such file is not found: $file_path");
        }

        $message = $this->execWithSocket(function ($socket) use ($file_path) {
            stream_socket_sendto($socket, "zINSTREAM\0");
            foreach ($this->splitFile($file_path) as $flaggment) {
                stream_socket_sendto($socket, $flaggment);
            }
            return stream_socket_recvfrom($socket, 65536);
        });

        return $this->checkMessage($message, 'OK');
    }

    /**
     * Prepare socket, then execute a callback, and then close socket.
     * The argument of the callback is socket.
     *
     * @param callable $callback
     * @return string
     */
    private function execWithSocket(callable $callback): string
    {
        $address = sprintf("tcp://%s:%s", $this->host, $this->port);
        $socket = stream_socket_client($address);
        if (! $socket) {
            throw new \RuntimeException('Connection failed: ' . $address);
        }

        $result = $callback($socket);

        fclose($socket);
        return $result;
    }

    /**
     * Check message equal to a specific string.
     *
     * @param string $message
     * @param string $check
     * @return boolean
     */
    private function checkMessage(string $message, string $check): bool
    {
        $mes = (strrchr($message, ":")) ? : ': '.$message;
        return trim(substr($mes, 1)) === $check;
    }

    /**
     * Split file contents to length and string.
     *
     * @param string $path
     * @return Generator
     */
    private function splitFile(string $path): Generator
    {
        $fp = fopen($path, 'rb');
        while (! feof($fp)) {
            $str = fread($fp, 1024);
            yield pack('N', strlen($str)).$str;
        }

        yield pack('N', 0);
    }
}
