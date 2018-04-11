<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:34
 */

namespace App\Service;

use App\Entity\Link;
use Doctrine\ORM\EntityManagerInterface;

class LinkManager
{
    const UUIG_LENGTH = 8;

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function random_str($length, $keyspace = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_~')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    function getUuid($len=self::UUIG_LENGTH) {
        $linkRepo = $this->em->getRepository(Link::class);

        do {
            $uuid = $this->random_str(8);
        } while (!$linkRepo->uniqueUuidCheck($uuid));

        return $uuid;
    }
}