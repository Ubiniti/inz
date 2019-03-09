<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRateRepository")
 */
class VideoRate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $video_hash;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $viewer_username;

    /**
     * @ORM\Column(type="boolean")
     */
    private $rate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideoHash(): ?string
    {
        return $this->video_hash;
    }

    public function setVideoHash(string $video_hash): self
    {
        $this->video_hash = $video_hash;

        return $this;
    }

    public function getViewerUsername(): ?string
    {
        return $this->viewer_username;
    }

    public function setViewerUsername(string $viewer_username): self
    {
        $this->viewer_username = $viewer_username;

        return $this;
    }

    public function getRate(): ?bool
    {
        return $this->rate;
    }

    public function setRate(bool $rate): self
    {
        $this->rate = $rate;

        return $this;
    }
}
