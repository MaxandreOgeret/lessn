<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:52
 */

namespace App\Repository;

use App\Entity\SBLink;
use App\Service\Commands\SafebrowsingCmdManager;
use App\Service\Commands\SafebrowsingFileManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Monolog\Logger;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class SBLinkRepository extends ServiceEntityRepository
{
    public const STR = 1;
    public const INT = 2;

    private $safebrowsingCmdManager;
    private $sbFileManager;
    private $linkSecLogger;

    public function __construct(
        RegistryInterface $registry,
        SafebrowsingCmdManager $safebrowsingCmdManager,
        SafebrowsingFileManager $sbFileManager,
        Logger $linkSecLogger
    ) {
        parent::__construct($registry, SBLink::class);
        $this->safebrowsingCmdManager = $safebrowsingCmdManager;
        $this->sbFileManager = $sbFileManager;
        $this->linkSecLogger = $linkSecLogger;
    }

    private function truncate()
    {
        $cmd = $this->getEntityManager()->getClassMetadata(SBLink::class);
        $connection = $this->getEntityManager()->getConnection();
        $connection->beginTransaction();
        try {
            $connection->query('truncate table '.$cmd->getTableName());
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }

    /**
     * @param $rawHashes
     * @param $prefixSize
     * @param $output
     * @param bool $update
     * @return mixed
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function setupHashes($rawHashes, $prefixSize, $output, $update = false)
    {
        if (!$update) {
            $this->truncate();
        }

        $hashArray = str_split($this->sbFileManager->decodeBinaryBase64($rawHashes), $prefixSize*2);
        $len = count($hashArray);
        unset($rawHashes);

        $progressBar = new ProgressBar($output, sizeof($hashArray));
        $progressBar->start();
        $progressBar->setRedrawFrequency(1000);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $connection = $this->getEntityManager()->getConnection();
        $connection->beginTransaction();
        for ($i = 0; $i < $len; $i++) {
            $hash = $hashArray[$i];
            unset($hashArray[$i]);

            $connection->query("INSERT INTO sblink (hash) values ('$hash')");
            $progressBar->advance();

            if ($i %500 === 0) {
                $connection->commit();
                $connection->beginTransaction();
            }
        }
        $connection->commit();
        $progressBar->finish();
        $this->linkSecLogger->info('SB : Inserted hashes in db', ['nb' => $len]);
        $output->writeln('');
        return $this->getChecksum();
    }

    public function addAndDel(string $additions, array $deletions, $prefixSize)
    {
        $additions = str_split(bin2hex(base64_decode($additions)), $prefixSize*2);
        $sql = 'select "applySbUpdate"('.
            $this->arrayToSql($additions, self::STR).', '.
            $this->arrayToSql($deletions, self::INT).")";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $this->linkSecLogger->info('SB : Inserted hashes in db', ['nb' => count($additions)]);
        $this->linkSecLogger->info('SB : Deleted hashes in db', ['nb' => count($deletions)]);
        return $stmt->fetch()['applySbUpdate'];
    }

    private function arrayToSql($stringArray, $type)
    {
        $returnString = 'ARRAY[';

        if ($type === self::STR) {
            $stringArray = array_map(function ($data) {
                return "'".$data."'";
            }, $stringArray);
        }

        $returnString = $returnString . implode(', ', $stringArray).']';
        $returnString = $returnString . ($type === self::STR ? '::TEXT[]' : '::INTEGER[]');
        return $returnString;
    }

    public function getChecksum()
    {
        $sql = 'select "getChecksum"()';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['getChecksum'];
    }

    public function areBanned($hashArray)
    {
        $result = $this->findBy(
            [
                'hash' => $hashArray
            ]
        );

        if (count($result) > 0) {
            $this->linkSecLogger->warn('SB : protection triggered', [$hashArray]);
            return true;
        }
        return false;
    }
}
