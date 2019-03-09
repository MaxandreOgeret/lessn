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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateBannedLinksCommand extends Command
{
    protected static $defaultName = 'lessn:banlinks:update';
    protected $rootDir;
    protected $phishTankKey;
    protected $em;

    /**
     * UpdateBannedLinksCommand constructor.
     *
     * @param $rootDir
     * @throws \Exception
     */
    public function __construct($rootDir, EntityManagerInterface $em)
    {
        $this->rootDir = $rootDir;

        if (!($this->phishTankKey = getenv('PHISHTANK_KEY'))) {
            throw new \Exception('Unable to get PhishTank key.');
        }

        $this->em = $em;

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
        $output->writeln([
            '',
            'LESSn - Banned URL Updater. ><(((°>',
            '============',
        ]);

        $output->writeln([
            '',
            'Removing old file',
        ]);

        $files = glob($this->rootDir.'/fishtank/online-valid.csv');
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file);
            }
        }

        $output->writeln([
            'Old file removed.',
            'Downloading new file.',
        ]);

        $url = "http://data.phishtank.com/data/$this->phishTankKey/online-valid.csv";
        file_put_contents($this->rootDir.'/fishtank/online-valid.csv', fopen($url, 'r'));

        $output->writeln([
            'New file downloaded.',
            'Purging table and putting new values.'
        ]);

        $connexion = $this->em->getConnection();
        $platform = $connexion->getDatabasePlatform();
        $connexion->executeUpdate($platform->getTruncateTableSQL('bannedlink', true));

        $csvFile = fopen($this->rootDir.'/fishtank/online-valid.csv', 'r');
        fgetcsv($csvFile);

        $linkNb = 0;
        while (($line = fgetcsv($csvFile))) {

            $phishLinks = $this->explodeLink($line[1]);

            foreach ($phishLinks as $key => $phishLink) {
                $formattedHost = $this->formatHost($phishLink);
                $this->putInBd($line, $formattedHost, $key);
                $linkNb++;
            }
        }

        $this->em->flush();
        fclose($csvFile);

        $output->writeln([
            "$linkNb link(s) inserted in database.",
            'Checking links already in DB.'
        ]);

        $output->writeln([
            "Removed ".$this->removePhishingLinks().' links.',
        ]);

    }

    /**
     * @param $line
     * @param $formattedHost
     * @param $key
     */
    private function putInBd($line, $formattedHost, $key)
    {
        if (strlen($line[1]) < 4096 && sizeof(array_filter(explode('.', $formattedHost))) >= 2) {
            $this->em->persist
            (
                new BannedLink
                (
                    $line[0]."_$key",
                    $line[1],
                    $line[3],
                    $formattedHost
                )
            );
        }
    }

    /**
     * @param $link
     * @return array
     */
    private function explodeLink($link)
    {
        $link = str_replace('https://', 'http://', $link);
        $exploded = array_filter(explode('http://', $link));

        foreach ($exploded as &$link) {
            if (substr($link, 0, 3) !== "www.") {
                $link = "http://www.".$link;
            }
        }

        return $exploded;
    }

    /**
     * @param $host
     * @return mixed|string
     */
    private function formatHost($host)
    {
        // Get Host
        $host = parse_url($host)['host'];

        // Remove www.
        $host = str_replace('/^www./', '', $host);

        // Get most generic host
        return implode('.', array_slice(explode('.', $host), -2, 2));
    }

    private function removePhishingLinks()
    {
        return $this->em->getRepository(Link::class)->removeBannedLinks();
    }
}