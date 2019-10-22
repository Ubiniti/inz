<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\VideoRate", mappedBy="video", orphanRemoval=true)
     */
    private $rates;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Comment",
     *     mappedBy="video",
     *     orphanRemoval=true,
     *     cascade={"persist","remove"}
     *     )
     */
    private $comments;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price;

    public function __construct()
    {
        $this->uploaded = new \DateTimeImmutable();
        $this->views = 0;
        $this->description = '';
        $this->category = '';
        $this->rates = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
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

    /**
     * @return Collection|VideoRate[]
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    public function addRate(VideoRate $rate): self
    {
        if (!$this->rates->contains($rate)) {
            $rate->setVideo($this);
            $this->rates[] = $rate;
        }

        return $this;
    }

    public function removeRate(VideoRate $rate): self
    {
        if ($this->rates->contains($rate)) {
            $this->rates->removeElement($rate);
            if ($rate->getVideo() === $this) {
                $rate->setVideo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $comment->setVideo($this);
            $this->comments[] = $comment;
        }

        return $this;
    }

    public function comment(string $message, UserInterface $user): self
    {
        $comment = (new Comment())
            ->setContents($message)
            ->setAuthorUsername($user->getUsername())
            ->setAdded(new \DateTime());

        $this->addComment($comment);

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getVideo() === $this) {
                $comment->setVideo(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }
}
