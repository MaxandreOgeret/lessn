<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SBLinkMetaRepository")
 * @ORM\Table(name="sblinkmeta")
 */
class SBLinkMeta
{
    /**
     * @var string
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $prefixSize;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $clientState;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $sha256Checksum;

    /**
     * @var integer
     * @ORM\Column(type="datetime")
     */
    private $datecrea;

    /**
     * SBLinkMeta constructor.
     * @param int $prefixSize
     * @param string $clientState
     * @param string $sha256Checksum
     * @throws \Exception
     */
    public function __construct(int $prefixSize, string $clientState, string $sha256Checksum)
    {
        $this->prefixSize = $prefixSize;
        $this->clientState = $clientState;
        $this->sha256Checksum = $sha256Checksum;
        $this->datecrea = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPrefixSize(): int
    {
        return $this->prefixSize;
    }

    /**
     * @param int $prefixSize
     * @return SBLinkMeta
     */
    public function setPrefixSize(int $prefixSize): SBLinkMeta
    {
        $this->prefixSize = $prefixSize;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientState(): string
    {
        return $this->clientState;
    }

    /**
     * @param string $clientState
     * @return SBLinkMeta
     */
    public function setClientState(string $clientState): SBLinkMeta
    {
        $this->clientState = $clientState;
        return $this;
    }

    /**
     * @return string
     */
    public function getSha256Checksum(): string
    {
        return $this->sha256Checksum;
    }

    /**
     * @param string $sha256Checksum
     * @return SBLinkMeta
     */
    public function setSha256Checksum(string $sha256Checksum): SBLinkMeta
    {
        $this->sha256Checksum = $sha256Checksum;
        return $this;
    }

    /**
     * @return int
     */
    public function getDatecrea(): int
    {
        return $this->datecrea;
    }

    /**
     * @return SBLinkMeta
     * @throws \Exception
     */
    public function updateDatecrea(): SBLinkMeta
    {
        $this->datecrea = new \DateTime();
        return $this;
    }
}
