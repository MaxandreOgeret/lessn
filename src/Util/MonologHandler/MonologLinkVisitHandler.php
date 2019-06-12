<?php


namespace App\Util\MonologHandler;

use App\Entity\LogLink;
use App\Service\Geolocalization\Geo2IpManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class MonologLinkVisitHandler extends AbstractProcessingHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    protected $geo2IpManager;

    /**
     * MonologLinkVisitHandler constructor.
     * @param EntityManagerInterface $em
     * @param Geo2IpManager $geo2IpManager
     */
    public function __construct(EntityManagerInterface $em, Geo2IpManager $geo2IpManager)
    {
        parent::__construct();
        $this->em = $em;
        $this->geo2IpManager = $geo2IpManager;
    }

    /**
     * Called when writing to our database
     * @param array $record
     */
    protected function write(array $record)
    {
        $logEntry = new LogLink();
        $logEntry->setMessage($record['message']);
        $logEntry->setLevel($record['level_name']);
        $logEntry->setIp(hash('sha512', $record['context']['ip']));
        $logEntry->setLink($record['context']['link']);
        $logEntry->setCountry($this->geo2IpManager->getCountryIsoCode($record['context']['ip']));

        $this->em->persist($logEntry);
        $this->em->flush();
    }
}
