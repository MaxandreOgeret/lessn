<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 07/03/2019
 * Time: 09:21
 */

namespace App\Command;


use App\Entity\BannedLink;
use App\Entity\Link;
use App\Service\UriManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SafeBrowsingUpdateLocalCommand extends Command
{
    protected static $defaultName = 'lessn:safebrowsing:update';
    protected $rootDir;
    protected $apiKey;
    protected $em;
    protected $uriManager;

    /**
     * UpdateBannedLinksCommand constructor.
     *
     * @param $rootDir
     * @throws \Exception
     */
    public function __construct($rootDir, EntityManagerInterface $em, UriManager $uriManager)
    {
        $this->rootDir = $rootDir;
        $this->uriManager = $uriManager;
        $this->em = $em;

        if (!($this->apiKey = getenv('SAFE_BROWSING_KEY'))) {
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
        $url = "https://safebrowsing.googleapis.com/v4/threatListUpdates:fetch?key=$this->apiKey";

        $data = '{
  "client": {
    "clientId":       "lessn",
    "clientVersion":  "'.exec('git describe --tags --abbrev=0').'"
  },
  "listUpdateRequests": [{
    "threatType":      "MALWARE",
    "platformType":    "ANY_PLATFORM",
    "threatEntryType": "URL",
    "state":           "",
    "constraints": {
      "region":                "EU",
      "supportedCompressions": ["RAW"]
    }
  }]
}';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $result = curl_exec($curl);
        curl_close($curl);
//        dump($result);

        dump($this->rootDir);
        file_put_contents($this->rootDir.'/response.txt', $result);
    }
}