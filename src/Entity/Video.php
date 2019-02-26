<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $hash;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $author_username;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploaded;

    /**
     * @ORM\Column(type="integer")
     */
    private $views;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="time")
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="integer")
     */
    private $thumbs_up;

    /**
     * @ORM\Column(type="integer")
     */
    private $thumbs_down;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthorUsername(): ?string
    {
        return $this->author_username;
    }

    public function setAuthorUsername(string $author_username): self
    {
        $this->author_username = $author_username;

        return $this;
    }

    public function getUploaded(): ?\DateTimeInterface
    {
        return $this->uploaded;
    }

    public function setUploaded(\DateTimeInterface $uploaded): self
    {
        $this->uploaded = $uploaded;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeInterface $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function toArray()
    {
        return [
            'hash' => $this->getHash(),
            'title' => $this->getTitle(),
            'author_username' => $this->getAuthorUsername(),
            'uploaded' => $this->getUploaded(),
            'views' => $this->getViews(),
            'description' => $this->getDescription(),
            'duration' => $this->getDuration(),
            'category' => $this->getCategory(),
            'thumbs_up' => $this->getThumbsUp(),
            'thumbs_down' => $this->getThumbsDown()
        ];
    }

    public function toEncryptableArray()
    {
        return [
            'title' => $this->getTitle(),
            'author_username' => $this->getAuthorUsername(),
            'uploaded' => $this->getUploaded(),
            'duration' => $this->getDuration(),
            'category' => $this->getCategory(),
        ];
    }

    public function generateHash()
    {
        $serialized = json_encode($this->toEncryptableArray());
        $hash = md5($serialized);
        $this->setHash($hash);

        return $hash;
    }

    public function getThumbsUp(): ?int
    {
        return $this->thumbs_up;
    }

    public function setThumbsUp(int $thumbs_up): self
    {
        $this->thumbs_up = $thumbs_up;

        return $this;
    }

    public function getThumbsDown(): ?int
    {
        return $this->thumbs_down;
    }

    public function setThumbsDown(int $thumbs_down): self
    {
        $this->thumbs_down = $thumbs_down;

        return $this;
    }
}
