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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SafeBrowsingUpdateCommand extends Command
{
    protected static $defaultName = 'lessn:safebrowsing:update';
    protected $safeBrowsingDir;
    protected $apiKey;
    protected $em;
    protected $uriManager;
    protected $SbManager;
    private $sbWrapper;

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
        SafebrowsingCmdManager $SbManager,
        SafebrowsingWrapper $sbWrapper
    ) {
        $this->safeBrowsingDir = $rootDir.'/Safebrowsing';
        $this->uriManager = $uriManager;
        $this->em = $em;
        $this->apiKey = getenv('SAFE_BROWSING_KEY');
        $this->SbManager = $SbManager;
        $this->sbWrapper = $sbWrapper;

        if (empty($this->apiKey) || $this->apiKey === 'changethis') {
            throw new \Exception('Unable to get safe browsing key.');
        }

        parent::__construct();
    }

    public function configure()
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Update list of banned links.');
        $this->setHelp('Update list of banned links.');
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
        $this->sbWrapper->partialUpdateLander($output);
        $output->writeln('Done!');
    }
}
