<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:52
 */

namespace App\Repository;

use App\Entity\SBLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class SBLinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SBLink::class);
    }

    private function truncate()
    {
        $cmd = $this->getEntityManager()->getClassMetadata(SBLink::class);
        $connection = $this->getEntityManager()->getConnection();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM '.$cmd->getTableName());
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->query('ALTER TABLE '.$cmd->getTableName().' AUTO_INCREMENT = 1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }

    public function deleteHashList($hashList)
    {
//        dump($hashList);

        $query = $this->createQueryBuilder('sblink')
            ->select('sblink.hash')
            ->orderBy('sblink.hash')
            ->setFirstResult(1)
            ->setMaxResults(1);

        dump($query->getQuery()->execute());
    }

    public function createHashes($rawHashes, $prefixSize, $output, $update = false)
    {
        if (!$update) {
            $this->truncate();
        }

        $hashArray = str_split(bin2hex(base64_decode($rawHashes)), $prefixSize*2);
        $len = count($hashArray);
        unset($rawHashes);

        $progressBar = new ProgressBar($output, sizeof($hashArray));
        $progressBar->start();
        $progressBar->setRedrawFrequency(100);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        for ($i = 0; $i < $len; $i++) {
            $hash = $hashArray[$i];
            unset($hashArray[$i]);

            $sbLink = new SBLink($hash);
            $this->getEntityManager()->persist($sbLink);
            $progressBar->advance();

            if ($i % 500 === 0) {
                $this->getEntityManager()->flush();
                $this->getEntityManager()->clear();
            }
        }
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
        $progressBar->finish();
    }
}
