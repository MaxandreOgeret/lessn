<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 07/03/2019
 * Time: 09:21
 */

namespace App\Command;

use App\Service\Commands\SafebrowsingCmdManager;
use App\Service\UriManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SafeBrowsingCommand extends Command
{
    protected static $defaultName = 'lessn:safebrowsing:setup';
    protected $safeBrowsingDir;
    protected $apiKey;
    protected $em;
    protected $uriManager;
    protected $SbManager;

    /**
     * SafeBrowsingCommand constructor.
     * @param $rootDir
     * @param EntityManagerInterface $em
     * @param UriManager $uriManager
     * @param SafebrowsingCmdManager $SbManager
     * @throws \Exception
     */
    public function __construct(
        $rootDir,
        EntityManagerInterface $em,
        UriManager $uriManager,
        SafebrowsingCmdManager $SbManager
    ) {
        $this->safeBrowsingDir = $rootDir.'/Safebrowsing';
        $this->uriManager = $uriManager;
        $this->em = $em;
        $this->apiKey = getenv('SAFE_BROWSING_KEY');
        $this->SbManager = $SbManager;

        if (empty($this->apiKey) || $this->apiKey === 'changethis') {
            throw new \Exception('Unable to get safe browsing key.');
        }

        parent::__construct();
    }

    public function configure()
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Create or update list of banned links.');
        $this->setHelp('Create or update list of banned links.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('LESSn - Safe Browsing database installer');
        $url = "https://safebrowsing.googleapis.com/v4/threatListUpdates:fetch?key=$this->apiKey";

        $output->writeln('Building and executing query');
        $data = $this->SbManager->buildJsonBody();
        $curl = $this->SbManager->curlInit($url, $data);
        $output->writeln('Saving hashes file...');
        $filePath = $this->SbManager->curlExecAndSave($curl, $this->safeBrowsingDir);

        $output->writeln('Saving hashes in db...');
        $this->SbManager->parseAndProcess($filePath, $output);

        $output->writeln('Done!');
    }
}
