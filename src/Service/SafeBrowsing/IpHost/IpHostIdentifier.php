<?php


namespace App\Service\SafeBrowsing\IpHost;

/**
 * Class IpHostIdentifier
 *
 * @package App\Service\SafeBrowsing\IpHost
 */
class IpHostIdentifier
{
    /**
     * Detect if host is an IP and the IP format.
     *
     * @param string $host hostname which we want to identify
     *
     * @return string
     */
    public function detecIpFormat($host)
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return IpHostManager::DOT_DEC_IP;
        } elseif ($this->isDecIp($host)) {
            return IpHostManager::DEC_IP;
        } elseif ($this->isHexIp($host)) {
            return IpHostManager::HEX_IP;
        } elseif ($this->isDotBinIp($host)) {
            return IpHostManager::DOT_BIN_IP;
        } else {
            return IpHostManager::NOT_IP;
        }
    }

    /**
     * Detect if host is a decimal IP.
     *
     * @param string $host hostname which we want to identify
     *
     * @return string
     */
    public function isDecIp($host)
    {
        return (is_numeric($host) && strlen($host) === 10 && (int)$host <= 4294967295);
    }

    /**
     * Detect if host is a hex IP.
     *
     * @param string $host hostname which we want to identify
     *
     * @return string
     */
    public function isHexIp($host)
    {
        $host = str_replace('0x', '', $host);
        return (ctype_xdigit($host) && strlen($host) === 8 && hexdec($host) <= 4294967295);
    }

    /**
     * Detect if host is a dot-binary IP.
     *
     * @param string $host hostname which we want to identify
     *
     * @return string
     */
    public function isDotBinIp($host)
    {
        if (sizeof(explode('.', $host)) !== 4) {
            return false;
        }

        $host = str_replace('.', '', $host);
        return (preg_match('~^[01]+$~', $host) && strlen($host) === 32 && bindec($host) <= 4294967295);
    }
}
