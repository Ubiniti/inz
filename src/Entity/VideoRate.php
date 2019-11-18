<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRateRepository")
 */
class VideoRate
{
    public const UP = 1;
    public const DOWN = 0;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     */
    private $rate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Video", inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $video;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

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

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?Video $video): self
    {
        $this->video = $video;

        return $this;
    }
}
