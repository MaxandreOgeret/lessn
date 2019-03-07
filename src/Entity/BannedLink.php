<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BannedLinkRepository")
 * @ORM\Table(name="bannedlink")
 */
class BannedLink
{


    /**
     * @var string
     * @ORM\Column(name="phish_id", type="string", length=12)
     * @ORM\Id()
     */
    private $phish_id;

    /**
     * @var string
     * @ORM\Column(name="url", type="string", length=4096)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="host", type="string", length=1024)
     */
    private $host;

    /**
     * @var string
     * @ORM\Column(name="submission_time", type="date")
     */
    private $submission_time;


    /**
     * BannedLink constructor.
     *
     * @param string $phish_id
     * @param string $url
     * @param string $submission_time
     */
    public function __construct(
        string $phish_id,
        string $url,
        string $submission_time,
        string $host
    ) {
        $this->phish_id = $phish_id;
        $this->url = $url;
        $this->submission_time = \DateTime::createFromFormat(DATE_ISO8601, $submission_time);
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getPhishId(): string
    {
        return $this->phish_id;
    }

    /**
     * @param string $phish_id
     * @return BannedLink
     */
    public function setPhishId(string $phish_id): BannedLink
    {
        $this->phish_id = $phish_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return BannedLink
     */
    public function setUrl(string $url): BannedLink
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhishDetailUrl(): string
    {
        return $this->phish_detail_url;
    }

    /**
     * @param string $phish_detail_url
     * @return BannedLink
     */
    public function setPhishDetailUrl(string $phish_detail_url): BannedLink
    {
        $this->phish_detail_url = $phish_detail_url;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubmissionTime(): string
    {
        return $this->submission_time;
    }

    /**
     * @param string $submission_time
     * @return BannedLink
     */
    public function setSubmissionTime(string $submission_time): BannedLink
    {
        $this->submission_time = $submission_time;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

}
