<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkRepository")
 * @ORM\Table(name="link", indexes={@Index(name="uuid_idx", columns={"id", "uuid"})})
 */
class Link
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     * @CustomAssert\validUuid()
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=1024)
     * @Assert\NotBlank()
     * @CustomAssert\validURL()
     * @Assert\Length(max="1024")
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="link")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $ipcrea;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $useragentcrea;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datecrea;

    /**
     * @var LogLink
     * @ORM\OneToMany(targetEntity="App\Entity\LogLink", mappedBy="link", cascade={"remove"})
     */
    private $logLink;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Link
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     * @return Link
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return Link
     */
    public function setUrl($url)
    {
        if(!strpos($url, '://')) {
            $url = 'http://'.$url;
        }
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Link
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatecrea()
    {
        return $this->datecrea;
    }

    /**
     * @param mixed $datecrea
     * @return Link
     */
    public function setDatecrea($datecrea)
    {
        $this->datecrea = $datecrea;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIpcrea()
    {
        return $this->ipcrea;
    }

    /**
     * @param mixed $ipcrea
     * @return Link
     */
    public function setIpcrea($ipcrea)
    {
        $this->ipcrea = $ipcrea;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUseragentcrea()
    {
        return $this->useragentcrea;
    }

    /**
     * @param mixed $useragentcrea
     * @return Link
     */
    public function setUseragentcrea($useragentcrea)
    {
        $this->useragentcrea = $useragentcrea;
        return $this;
    }

    /**
     * @return LogLink
     */
    public function getLogLink()
    {
        return $this->logLink;
    }

    /**
     * @param LogLink $logLink
     * @return Link
     */
    public function setLogLink(LogLink $logLink): Link
    {
        $this->logLink = $logLink;
        return $this;
    }



    public function __construct($request)
    {
        $this->ipcrea = hash('sha512', $request->getClientIp());
        $this->useragentcrea = hash('sha512', $request->headers->get('User-Agent'));
        $this->datecrea = new \DateTime();
        dump($this->datecrea);
    }

}
