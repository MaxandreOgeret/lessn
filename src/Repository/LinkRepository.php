<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:52
 */

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Link::class);
    }

    public function uniqueUuidCheck($uuid)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->where('l.uuid = (:uuid)')
            ->setParameter('uuid', $uuid)
        ;

        return $qb->getQuery()->getSingleScalarResult() == 0;
    }

    public function countLastByIpUa($ip, $ua)
    {
        $fromWhen = new \DateTime("-1 minute");

        $qb = $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->where('l.ipcrea = (:ip)')
            ->andWhere('l.useragentcrea = (:ua)')
            ->andWhere('l.datecrea > (:date)')
            ->setParameters(['ip' => $ip, 'date' => $fromWhen, 'ua' => $ua])
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }
}