<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:34
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    private $em;
    private $locales;

    public function __construct(EntityManagerInterface $em, $locales)
    {
        $this->em = $em;
        $this->locales = $locales;
    }

    public function updateUserLocale(User $user, string $locale)
    {
        if ($user->getLocale() === $locale) {
            return;
        }

        $user->setLocale($locale);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function getLocalesArray()
    {
        return explode('|', $this->locales);
    }
}
