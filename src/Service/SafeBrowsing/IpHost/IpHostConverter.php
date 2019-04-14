<?php


namespace App\Service\SafeBrowsing\IpHost;



class IpHostConverter
{
    public function dotBin2DotDec($hostname)
    {
        $binary = str_replace('.', '', $hostname);
        return $this->bin2DotDec($binary);
    }

    public function dec2DotDec($hostname)
    {
        $binary = decbin($hostname);
        return $this->bin2DotDec($binary);
    }

    public function hex2DotDec($hostname)
    {
        $binary = decbin(hexdec($hostname));
        return $this->bin2DotDec($binary);
    }

    public function bin2DotDec($hostname)
    {
        $binary = sprintf('%032s', $hostname);
        $split = str_split($binary, 8);
        $converted = array_map('bindec', $split);
        return implode('.', $converted);
    }
}