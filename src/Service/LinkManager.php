<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:34
 */

namespace App\Service;

use App\Entity\Link;
use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class LinkManager
{
    const UUIG_LENGTH = 8;
    const MAX_SPAM = 5;

    private $em;

    /**
     * LinkManager constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $length
     * @param string $keyspace
     * @return string
     * @throws \Exception
     */
    private function random_str($length, $keyspace = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_~')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    /**
     * @param int $len
     * @return string
     * @throws \Exception
     */
    function getUuid($len=self::UUIG_LENGTH) {
        $linkRepo = $this->em->getRepository(Link::class);

        do {
            $uuid = $this->random_str(8);
        } while (!$linkRepo->uniqueUuidCheck($uuid));

        return $uuid;
    }

    /**
     * @param $ip
     * @param $em
     * @return bool
     */
    function spamProtection($ip, $ua, $em) {
        /** @var $em EntityManagerInterface */
        $count = $em->getRepository(Log::class)->countLastByIpUa($ip, $ua);
        return $count > self::MAX_SPAM;
    }

    function apiLinkCheck($link) {
        $request = new Request();
        $request->setMethod('POST');
    }
}