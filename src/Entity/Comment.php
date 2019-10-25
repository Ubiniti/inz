<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Video", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $video;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comment", inversedBy="subComments")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="parent")
     */
    private $subComments;

    public function __construct(?Comment $parent = null)
    {
        $this->subComments = new ArrayCollection();
        $this->added = new DateTime();

        if ($parent !== null) {
            $this->video = $parent->video;
            $this->parent = $parent;
        }
    }

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

    public function getAdded(): ?DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(DateTimeInterface $added): self
    {
        $this->added = $added;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSubComments(): Collection
    {
        return $this->subComments;
    }

    public function addSubcomment(self $subcomment): self
    {
        if (!$this->subComments->contains($subcomment)) {
            $this->subComments[] = $subcomment;
            $subcomment->setParent($this);
        }

        return $this;
    }

    public function removeSubcomment(self $subcomment): self
    {
        if ($this->subComments->contains($subcomment)) {
            $this->subComments->removeElement($subcomment);
            // set the owning side to null (unless already changed)
            if ($subcomment->getParent() === $this) {
                $subcomment->setParent(null);
            }
        }

        return $this;
    }
}
