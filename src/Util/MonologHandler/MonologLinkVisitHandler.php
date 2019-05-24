<?php


namespace App\Util\MonologHandler;

use App\Entity\LogLink;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class MonologLinkVisitHandler extends AbstractProcessingHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * MonologDBHandler constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
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

        $this->em->persist($logEntry);
        $this->em->flush();
    }
}
