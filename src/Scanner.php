<?php
namespace Niisan\ClamAV;

interface Scanner
{

    /**
     * Ping to scanner server or socket
     * 
     * When the scan server is not connected,
     * throw RuntimeException.
     *
     * @return boolean
     * @throws \RuntimeException 
     */
    public function ping() :bool;

    /**
     * Scan a file.
     * 
     * When the file have some virus, this method return false,
     * otherwise return true.
     *
     * @param string $file_path
     * @return boolean
     */
    public function scan(string $file_path): bool;
}