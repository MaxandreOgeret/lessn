<?php


namespace App\Service\Geolocalization;

use GeoIp2\Database\Reader;

class Geo2IpManager
{
    private $dbReader;

    /**
     * Geo2IpManager constructor.
     */
    public function __construct($rootDir)
    {
        $this->dbReader = new Reader($rootDir.'/GeoIp2/GeoLite2-Country.mmdb');
    }

    public function getCountryIsoCode($ip)
    {
        try {
            return $this->dbReader->country($ip)->country->isoCode;
        } catch (\Exception $e) {
            return null;
        }
    }
}
