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
use Monolog\Logger;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LinkManager
{
    const UUID_LENGTH = 8;
    const MAX_SPAM = 5;

    private $em;
    private $validator;
    private $linkSecLogger;
    private $uriManager;

    /**
     * LinkManager constructor.
     *
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @param Logger $linkSecLogger
     */
    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        Logger $linkSecLogger,
        UriManager $uriManager
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->linkSecLogger = $linkSecLogger;
        $this->uriManager = $uriManager;
    }

    /**
     * @param $length
     * @param string $keyspace
     * @return string
     * @throws \Exception
     */
    private function randomStr($length, $keyspace = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_')
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
    public function getUuid($len = self::UUID_LENGTH)
    {
        $linkRepo = $this->em->getRepository(Link::class);

        do {
            $uuid = $this->randomStr($len);
        } while (!$linkRepo->uniqueUuidCheck($uuid));

        return $uuid;
    }

    /**
     * @param $ip
     * @param $em
     * @return bool
     */
    public function spamProtection($ip, $ua, $em)
    {
        /** @var $em EntityManagerInterface */
        $count = $em->getRepository(Link::class)->countLastByIpUa($ip, $ua);

        if ($count > self::MAX_SPAM) {
            $this->linkSecLogger->warn('Spam protection triggered');
            return true;
        }

        return false;
    }

    public function createOrUpdate($linkArray, $request, $user)
    {
        $repo = $this->em->getRepository(Link::class);
        /** @var Link $link */
        if (key_exists('id', $linkArray)) {
            $link = $repo->find($linkArray['id']);
        } else {
            $link = null;
        }

        // If this is a new link
        if (is_null($link)) {
            $link = new Link($request);
            $link
                ->setUuid($linkArray['uuid'])
                ->setUrl($this->uriManager->format($linkArray['url']))
                ->setUser($user);
        } else {
            $linkSave = clone $link;
            $link
                ->setUrl($linkArray['url'])
                ->setUuid($linkArray['uuid']);
        }

        $errors = $this->validator->validate($link);
        if (count($errors) > 0) {
            return isset($linkSave) ? $linkSave : null;
        } else {
            $this->em->persist($link);
            $this->em->flush();
            return $link;
        }
    }

    public function delete($linkArray)
    {
        $repo =$this->em->getRepository(Link::class);
        /** @var Link $link */
        $link = $repo->find($linkArray['id']);
        $this->em->remove($link);
        $this->em->flush();
    }

    public function getLinkFromUuid($uuid)
    {
        return $this->em->getRepository(Link::class)->findOneBy(['uuid' => $uuid]);
    }
}
