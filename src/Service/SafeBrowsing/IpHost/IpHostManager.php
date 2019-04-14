<?php


namespace App\Service\SafeBrowsing\IpHost;



class IpHostManager
{
    const NOT_IP = 0;
    const DEC_IP = 1;
    const DOT_DEC_IP = 2;
    const HEX_IP = 3;
    const DOT_BIN_IP = 4;

    private $ipHostIdentifier;
    private $ipHostConverter;

    public function __construct(IpHostIdentifier $ipHostIdentifier, IpHostConverter $ipHostConverter)
    {
        $this->ipHostIdentifier = $ipHostIdentifier;
        $this->ipHostConverter = $ipHostConverter;
    }

    public function handleIfIp($hostname)
    {
        switch ($this->ipHostIdentifier->detecIpFormat($hostname)) {
            case self::DOT_DEC_IP:
            case self::NOT_IP:
                return $hostname;

            case self::DEC_IP:
                return $this->ipHostConverter->dec2DotDec($hostname);

            case self::HEX_IP:
                return $this->ipHostConverter->hex2DotDec($hostname);

            case self::DOT_BIN_IP:
                return $this->ipHostConverter->dotBin2DotDec($hostname);
        }
    }

}