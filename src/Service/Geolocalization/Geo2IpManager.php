<?php


namespace App\Service\Geolocalization;

use App\Command\Geo2ApiCommand;
use GeoIp2\Database\Reader;
use Monolog\Logger;

class Geo2IpManager
{
    private $dbReader;
    private $geo2IpLogger;

    /**
     * Geo2IpManager constructor.
     * @param $rootDir
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function __construct($rootDir, Logger $geo2IpLogger)
    {
        $this->geo2IpLogger = $geo2IpLogger;
        try {
            $this->dbReader = new Reader($rootDir.'/Databases/'.Geo2ApiCommand::DB_FILENAME.'.mmdb');
        } catch (\Exception $e) {
            return;
        }
    }

    public function getCountryIsoCode($ip)
    {
        if (is_null($this->dbReader)) {
            $this->geo2IpLogger->critical('Unable to create dbReader class');
            return null;
        }

        try {
            return $this->dbReader->country($ip)->country->isoCode;
        } catch (\Exception $e) {
            $this->geo2IpLogger->info('Unable to get IP from database');
            return null;
        }
    }
}
