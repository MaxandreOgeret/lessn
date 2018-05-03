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
use App\Validator\Constraints\validURLValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LinkManager
{
    const UUIG_LENGTH = 8;
    const MAX_SPAM = 5;

    private $em;
    private $validator;

    /**
     * LinkManager constructor.
     *
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
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
        $count = $em->getRepository(Link::class)->countLastByIpUa($ip, $ua);

        return $count > self::MAX_SPAM;
    }

    function createOrUpdate($linkArray, $request, $user) {
        $repo =$this->em->getRepository(Link::class);
        /** @var Link $link */
        $link = $repo->findOneById($linkArray['id']);

        // If this is a new link
        if (is_null($link)) {
            $link = new Link($request);
            $link
                ->setId($linkArray['id'])
                ->setUuid($linkArray['uuid'])
                ->setUrl($linkArray['url'])
                ->setUser($user);
        } else {
            $linkSave = clone $link;
            $link
                ->setUrl($linkArray['url'])
                ->setUuid($linkArray['uuid']);
        }

        $errors = $this->validator->validate($link);
        if (count($errors) > 0) {
            return $linkSave;
        } else {
            $this->em->persist($link);
            $this->em->flush();
            return $link;
        }

    }

    function delete($linkArray) {
        $repo =$this->em->getRepository(Link::class);
        /** @var Link $link */
        $link = $repo->findOneById($linkArray['id']);
        $this->em->remove($link);
        $this->em->flush();
    }
}