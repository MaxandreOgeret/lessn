<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:52
 */

namespace App\Repository;

use App\Entity\LogLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class LogLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogLink::class);
    }

    public function getCountryLog($link)
    {
        $qb = $this->createQueryBuilder('ll')
            ->select('ll.country, count(ll.id)')
            ->where('ll.link = :link')
            ->groupBy('ll.country')
            ->setParameter('link', 11);

        $result = $qb->getQuery()->getResult();
        return array_combine(array_column($result, 'country'), array_column($result, 1));
    }
}
