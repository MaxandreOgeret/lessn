<?php


namespace App\Service\SafeBrowsing\IpHost;

/**
 * Class IpHostConverter
 *
 * @package App\Service\SafeBrowsing\IpHost
 */
class IpHostConverter
{
    /**
     * Convert dot-binary ip to dot decimal
     *
     * @param string $hostname ip to convert
     *
     * @return string
     */
    public function dotBin2DotDec($hostname)
    {
        $binary = str_replace('.', '', $hostname);
        return $this->bin2DotDec($binary);
    }

    /**
     * Convert decimal ip to dot decimal
     *
     * @param string $hostname ip to convert
     *
     * @return string
     */
    public function dec2DotDec($hostname)
    {
        $binary = decbin($hostname);
        return $this->bin2DotDec($binary);
    }

    /**
     * Convert hexadecimal ip to dot decimal
     *
     * @param string $hostname ip to convert
     *
     * @return string
     */
    public function hex2DotDec($hostname)
    {
        $binary = decbin(hexdec($hostname));
        return $this->bin2DotDec($binary);
    }

    /**
     * Convert binary ip to dot decimal
     *
     * @param string $hostname ip to convert
     *
     * @return string
     */
    public function bin2DotDec($hostname)
    {
        $binary = sprintf('%032s', $hostname);
        $split = str_split($binary, 8);
        $converted = array_map('bindec', $split);
        return implode('.', $converted);
    }
}
