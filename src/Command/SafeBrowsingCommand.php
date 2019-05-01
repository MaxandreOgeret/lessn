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

class SafeBrowsingCommand extends Command
{
    protected static $defaultName = 'lessn:safebrowsing:setup';
    protected $SbManager;
    protected $sbWrapper;

    /**
     * SafeBrowsingCommand constructor.
     * @param SafebrowsingCmdManager $SbManager
     * @param SafebrowsingWrapper $sbWrapper
     * @throws \Exception
     */
    public function __construct(
        SafebrowsingCmdManager $SbManager,
        SafebrowsingWrapper $sbWrapper
    ) {
        $this->SbManager = $SbManager;
        $this->sbWrapper = $sbWrapper;
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
        $this->sbWrapper->fullUpdateLander($output);
        $output->writeln('Done!');
    }
}
