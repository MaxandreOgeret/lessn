<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:52
 */

namespace App\Repository;

use App\Entity\SBLinkMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class SBLinkMetaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SBLinkMeta::class);
    }

    public function getState()
    {
        /** @var SBLinkMeta $sbLinkMeta */
        $sbLinkMeta = $this->findAll()[0];
        return $sbLinkMeta->getClientState();
    }

    public function createMetaData($checksum, $newClientState, $prefixSize)
    {
        $fetched = $this->findAll();

        /** @var SBLinkMeta $entity */
        if ($fetched === []) {
            $entity = new SBLinkMeta($prefixSize, $newClientState, $checksum);
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
            return;
        }

        $entity = $fetched[0];
        $entity->setClientState($newClientState);
        $entity->setSha256Checksum($checksum);
        $entity->setPrefixSize($prefixSize);
        $entity->updateDatecrea();
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function getHashLength()
    {
        /** @var SBLinkMeta $entity */
        $fetched = $this->findAll();
        $entity = $fetched[0];
        return $entity->getPrefixSize();
    }
}
