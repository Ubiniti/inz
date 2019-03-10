<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $contents;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $author_username;

    /**
     * @ORM\Column(type="datetime")
     */
    private $added;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $likes = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $dislikes = 0;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $parrent_hash;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $hash;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $video_hash;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContents(): ?string
    {
        return $this->contents;
    }

    public function setContents(string $contents): self
    {
        $this->contents = $contents;

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

    public function getAdded(): ?\DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): self
    {
        $this->added = $added;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(int $dislikes): self
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    public function getParrentHash(): ?string
    {
        return $this->parrent_hash;
    }

    public function setParrentHash(?string $parrent_hash): self
    {
        $this->parrent_hash = $parrent_hash;

        return $this;
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

    public function toEncryptableArray()
    {
        return [
            'author_username' => $this->getAuthorUsername(),
            'added' => $this->getAdded(),
            'parrent_hash' => $this->getParrentHash(),
            'video_hash' => $this->getVideoHash()
        ];
    }

    public function generateHash()
    {
        $serialized = json_encode($this->toEncryptableArray());
        $hash = md5($serialized);
        $this->setHash($hash);

        return $hash;
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
}
