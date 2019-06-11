<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 07/03/2019
 * Time: 09:21
 */

namespace App\Command;

use App\Service\Commands\SafebrowsingCmdManager;
use App\Service\Commands\SafebrowsingWrapper;
use App\Service\UriManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Geo2ApiCommand extends Command
{
    const DB_FILENAME = 'GeoLite2-Country';

    protected static $defaultName = 'lessn:geo2api:setup';
    private $saveDir;
    private $geo2IpLogger;

    /**
     * Geo2ApiCommand constructor.
     * @param $rootDir
     */
    public function __construct($rootDir, Logger $geo2IpLogger)
    {
        $this->saveDir = $rootDir.'/Databases';
        $this->geo2IpLogger = $geo2IpLogger;
        parent::__construct();
    }

    public function configure()
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Create or update country/ip database.');
        $this->setHelp('Create or update country/ip database');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('LESSn - Geo2Api database installer');
        $output->writeln('Downloading database...');

        try {
            $this->geo2IpLogger->info('Starting update of geo2ip db');

            file_put_contents(
                $this->saveDir.'/'.self::DB_FILENAME.'.tar.gz',
                fopen('https://geolite.maxmind.com/download/geoip/database/'.self::DB_FILENAME.'.tar.gz', 'r')
            );
        } catch (\Exception $e) {
            $this->geo2IpLogger->critical('Unable to download database');
            return;
        }

        $output->writeln('Extracting...');

        try {
            $gz = new \PharData($this->saveDir.'/'.self::DB_FILENAME.'.tar.gz');
            $gz->decompress();
            unset($gz);

            $tar = new \PharData($this->saveDir.'/'.self::DB_FILENAME.'.tar');

            $rootFoldder = $this->getTarRootFolder($tar);

            $tar->extractTo($this->saveDir, $rootFoldder.'/'.self::DB_FILENAME.'.mmdb', true);
        } catch (\Exception $e) {
            $this->geo2IpLogger->critical('Unable to extract database');
            return;
        }

        $output->writeln('Purging ald files...');

        try {
            rename(
                $this->saveDir.'/'.$rootFoldder.'/'.self::DB_FILENAME.'.mmdb',
                $this->saveDir.'/'.self::DB_FILENAME.'.mmdb'
            );
            unlink($this->saveDir.'/'.self::DB_FILENAME.'.tar.gz');
            unlink($this->saveDir.'/'.self::DB_FILENAME.'.tar');
            rmdir($this->saveDir.'/'.$rootFoldder);
        } catch (\Exception $e) {
            $this->geo2IpLogger->critical('Unable to purge old files');
            return;
        }

        $this->geo2IpLogger->info('Update of geo2ip db Done!');

        $output->writeln('Done!');
    }

    private function getTarRootFolder(\PharData $tar)
    {
        /** @var \PharFileInfo $file */
        foreach ($tar as $file) {
            return $file->getFilename();
        }
    }
}
