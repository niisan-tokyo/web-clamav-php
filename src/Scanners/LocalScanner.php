<?php
namespace Niisan\ClamAV\Scanners;

use Niisan\ClamAV\Scanner;

class LocalScanner implements Scanner
{

    private $path;

    public function __construct(array $options = [])
    {
        if (! isset($options['path'])) {
            throw new \RuntimeException("Socket path not given, i.e. ['path' => /var/run/clamav/clamd.ctl");
        }

        $this->path = $options['path'];
    }

    /**
     * @inheritDoc
     */
    public function ping(): bool
    {
        $message = $this->execWithSocket(function ($socket) {
            socket_send($socket, 'PING', 4, 0);
            socket_recv($socket, $buf, 65536, MSG_WAITALL);
            return $buf;
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
        chmod($file_path, '0644');
        $message = $this->execWithSocket(function ($socket) use ($file_path) {
            $command = 'SCAN ' . $file_path;
            socket_send($socket, $command, strlen($command), 0);
            socket_recv($socket, $buf, 65536, MSG_WAITALL);
            return $buf;
        });

        chmod($file_path, '0600');
        return $this->checkMessage($message, 'OK');
    }

    private function execWithSocket(callable $callback): string
    {
        $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
        socket_connect($socket, $this->path);

        $result = $callback($socket);

        socket_close($socket);
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
}