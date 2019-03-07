<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:52
 */

namespace App\Repository;

use App\Entity\BannedLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BannedLinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BannedLink::class);
    }

    public function isBanned($link)
    {
        $qb = $this->createQueryBuilder('bl')
            ->select('bl.phish_id')
            ->where('bl.host = :link')
            ->setParameter('link', $link);

        return (bool) $qb->getQuery()->getArrayResult();
    }
}