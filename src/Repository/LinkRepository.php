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
use Doctrine\Common\Persistence\ManagerRegistry;

class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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

        $hashIp = hash('sha512', $ip);
        $hashUa = hash('sha512', $ua);

        $qb = $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->where('l.ipcrea = (:ip)')
            ->andWhere('l.useragentcrea = (:ua)')
            ->andWhere('l.datecrea > (:date)')
            ->setParameters(['ip' => $hashIp, 'date' => $fromWhen, 'ua' => $hashUa])
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getByUser($user)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.user = (:user)')
            ->setParameters(['user' => $user])
            ->orderBy('l.datecrea', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    public function removeBannedLinks()
    {
        $session = "SET SESSION group_concat_max_len = 1000000;";
        $sql = "select id from link where link.url REGEXP(select GROUP_CONCAT(concat('/', bannedlink.host,".
            " '|/www.', bannedlink.host, '|.', bannedlink.host)	SEPARATOR '|') from bannedlink);";

        $conn = $this->getEntityManager()->getConnection();
        $conn->exec($session);
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $idsToRemove = array_column($stmt->fetchAll(), 'id');

        foreach ($idsToRemove as $value) {
            $link = $this->findOneBy(['id' => $value]);
            $this->getEntityManager()->remove($link);
        }
        $this->getEntityManager()->flush();

        return sizeof($idsToRemove);
    }
}
