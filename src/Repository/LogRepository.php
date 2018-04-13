<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:52
 */

namespace App\Repository;

use App\Entity\Link;
use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function countLastByIp($ip)
    {
        $fromWhen = new \DateTime("2 minutes ago");

        $qb = $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->where('l.ip = (:ip)')
            ->andWhere('l.date > (:date)')
            ->setParameters(['ip' => $ip, 'date' => $fromWhen])
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }
}