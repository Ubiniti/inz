<?php

namespace App\Entity;

use App\Entity\Interfaces\NormalizableInterface;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment implements NormalizableInterface
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
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="parent", cascade={"remove"})
     */
    private $subComments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentRate", mappedBy="comment", orphanRemoval=true, cascade={"persist"})
     */
    private $commentRates;

    public function __construct(?Comment $parent = null)
    {
        $this->subComments = new ArrayCollection();
        $this->added = new DateTime();

        if ($parent !== null) {
            $this->video = $parent->video;
            $this->parent = $parent;
        }
        $this->commentRates = new ArrayCollection();
    }

    public function rate(bool $rate, User $author)
    {
        $commentRate = (new CommentRate())
            ->setRate($rate)
            ->setComment($this)
            ->setAuthor($author);

        $this->addCommentRate($commentRate);
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

    /**
     * @return Collection|CommentRate[]
     */
    public function getCommentRates(): Collection
    {
        return $this->commentRates;
    }

    public function addCommentRate(CommentRate $commentRate): self
    {
        if (!$this->commentRates->contains($commentRate)) {
            $this->commentRates[] = $commentRate;
            $commentRate->setComment($this);
        }

        return $this;
    }

    public function removeCommentRate(CommentRate $commentRate): self
    {
        if ($this->commentRates->contains($commentRate)) {
            $this->commentRates->removeElement($commentRate);
            // set the owning side to null (unless already changed)
            if ($commentRate->getComment() === $this) {
                $commentRate->setComment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CommentRate[]
     */
    public function getCommentRatesUp(): Collection
    {
        $ratesUp = $this->commentRates->filter(function (CommentRate $commentRate) {
            dump($commentRate->getRate(), CommentRate::UP, $commentRate->getRate() === CommentRate::UP);
            return $commentRate->getRate() === CommentRate::UP;
        });

        return $ratesUp;
    }

    /**
     * @return Collection|CommentRate[]
     */
    public function getCommentRatesDown(): Collection
    {
        $ratesDown = $this->commentRates->filter(function (CommentRate $commentRate) {
            return $commentRate->getRate() === CommentRate::DOWN;
        });

        return $ratesDown;
    }

    public function normalize()
    {
        $array = [];
        $array['id'] = $this->id;
        $array['contents'] = $this->contents;
        $array['author_username'] = $this->author_username;
        $array['added'] = $this->added;
        $array['video'] = $this->getVideo()->getHash();
        $array['parent'] = $this->getParent() ? $this->getParent()->getId() : null;

        return $array;
    }
}
