<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogLinkRepository")
 * @ORM\Table(name="loglink")
 */
class LogLink
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=39)
     * @Assert\NotBlank()
     */
    private $ip;

    /**
     * @var Link
     * @ORM\ManyToOne(targetEntity="App\Entity\Link", inversedBy="logLink")
     */
    private $link;

    public function __construct($request, $link)
    {
        $this->ip = $request->getClientIp();
        $this->date = new \DateTime();
        $this->link = $link;
    }
}
