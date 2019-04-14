<?php


namespace App\Service\SafeBrowsing;


class IpHostManager
{
    const NOT_IP = 0;
    const DEC_IP = 1;
    const DOT_DEC_IP = 2;
    const HEX_IP = 3;
    const DOT_BIN_IP = 4;


    public function handleIfIp($hostname)
    {
        /*dump*/($this->detecIpFormat($hostname));

        return $hostname;
    }

    public function detecIpFormat($host)
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return self::DOT_DEC_IP;
        } else if ($this->isDecIp($host)) {
            return self::DEC_IP;
        } else if ($this->isHexIp($host)) {
            return self::HEX_IP;
        } else if ($this->isDotBinIp($host)) {
            return self::DOT_BIN_IP;
        } else {
            return self::NOT_IP;
        } 
    }

    public function isDecIp($host)
    {
        return (is_numeric($host) && strlen($host) === 10 && (int)$host<=4294967295 );
    }

    public function isHexIp($host)
    {
        $host = str_replace('0x', '', $host);
        return (ctype_xdigit($host) && strlen($host) === 8 && hexdec($host)<=4294967295);
    }

    public function isDotBinIp($host)
    {
        dump(sizeof(explode('.', $host)));
        $host = str_replace('.', '', $host);
        return (preg_match('~^[01]+$~', $host) && strlen($host) === 32 && bindec($host)<=4294967295);
    }
}