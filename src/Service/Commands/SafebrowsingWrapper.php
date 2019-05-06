<?php


namespace App\Service\Commands;

use App\Service\UriManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SafebrowsingWrapper
{
    const FULL=1;
    const PARTIAL=2;

    protected $safeBrowsingDir;
    protected $apiKey;
    protected $em;
    protected $uriManager;
    protected $SbManager;
    private $sbApiManager;
    private $sbFileManager;

    /**
     * SafebrowsingWrapper constructor.
     * @param $rootDir
     * @param EntityManagerInterface $em
     * @param UriManager $uriManager
     * @param SafebrowsingCmdManager $SbManager
     * @param SafebrowsingApiManager $sbApiManager
     * @param SafebrowsingFileManager $sbFileManager
     * @throws \Exception
     */
    public function __construct(
        $rootDir,
        EntityManagerInterface $em,
        UriManager $uriManager,
        SafebrowsingCmdManager $SbManager,
        SafebrowsingApiManager $sbApiManager,
        SafebrowsingFileManager $sbFileManager
    ) {
        $this->safeBrowsingDir = $rootDir.'/Safebrowsing';
        $this->uriManager = $uriManager;
        $this->em = $em;
        $this->apiKey = getenv('SAFE_BROWSING_KEY');
        $this->SbManager = $SbManager;
        $this->sbApiManager = $sbApiManager;
        $this->sbFileManager = $sbFileManager;

        if (empty($this->apiKey) || $this->apiKey === 'changethis') {
            throw new \Exception('Unable to get safe browsing key.');
        }
    }

    public function fullUpdateLander(OutputInterface $output)
    {
        $this->fullOrPartialUpdate($output, self::FULL);
    }

    public function partialUpdateLander(OutputInterface $output)
    {
        $this->fullOrPartialUpdate($output, self::PARTIAL);
    }

    private function fullOrPartialUpdate(OutputInterface $output, $type = self::FULL)
    {
        $url = "https://safebrowsing.googleapis.com/v4/threatListUpdates:fetch?key=$this->apiKey";

        $output->writeln('Building and executing query');
        $data = $this->sbApiManager->buildJsonBody($type === self::PARTIAL);

        $curl = $this->sbApiManager->curlInit($url, $data);
        $output->writeln('Saving hashes file...');

        $filename = $type === self::FULL ? 'SBresponse.txt' : 'SBupdate.txt';
        $filePath = $this->sbApiManager->curlExecAndSave($curl, $this->safeBrowsingDir, $filename);
        
        $jsonA = $this->sbFileManager->parse($filePath, $output);
        $this->SbManager->process($output, $jsonA);
    }
}
