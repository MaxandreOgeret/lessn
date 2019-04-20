<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SBLinkRepository")
 * @ORM\Table(name="sblink", indexes={@Index(name="hash_idx", columns={"id", "hash"})})
 */
class SBLink
{
    /**
     * @var string
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="hash", type="string", length=32)
     */
    private $hash;

    /**
     * SBLink constructor.
     * @param string $hash
     */
    public function __construct(string $hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return SBLink
     */
    public function setHash(string $hash): SBLink
    {
        $this->hash = $hash;
        return $this;
    }
}
