<?php

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
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
     * @ORM\Column(type="integer", nullable=true, options={"default" : "0"})
     */
    private $price = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Channel", inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channel;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="video")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Playlist", mappedBy="videos")
     */
    private $playlists;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPublic = true;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="paidForVideos")
     */
    private $usersWithAccess;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $allowsAds;

    public function __construct()
    {
        $this->uploaded = new DateTimeImmutable();
        $this->views = 0;
        $this->description = '';
        $this->rates = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->playlists = new ArrayCollection();
        $this->usersWithAccess = new ArrayCollection();
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

    public function getUploaded(): ?DateTimeInterface
    {
        return $this->uploaded;
    }

    public function setUploaded(DateTimeInterface $uploaded): self
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

    public function toEncryptableArray()
    {
        return [
            'title' => $this->getTitle(),
            'author_username' => $this->getAuthorUsername(),
            'uploaded' => $this->getUploaded(),
            'duration' => $this->getDuration(),
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
            ->setAuthorUsername($user->getUsername());

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

    public function getPriceAsCurrency(): ?float
    {
        return $this->price/100;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addVideo($this);
        }

        return $this;
    }

//    public function setCategories(Collection $categories)
//    {
//        $this->categories = $categories;
//
//        return $this;
//    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeVideo($this);
        }

        return $this;
    }

    /**
     * @return Collection|Playlist[]
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
            $playlist->addVideo($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->contains($playlist)) {
            $this->playlists->removeElement($playlist);
            $playlist->removeVideo($this);
        }

        return $this;
    }

    public function getDurationInReadableFormat(): string
    {
        if (!$this->duration) {
            return '';
        }
        $format = $this->duration >= 60*60 ? 'h:i:s' : 'i:s';

        return date($format, $this->duration);
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(?bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsersWithAccess(): Collection
    {
        return $this->usersWithAccess;
    }

    public function addUsersWithAccess(User $usersWithAccess): self
    {
        if (!$this->usersWithAccess->contains($usersWithAccess)) {
            $this->usersWithAccess[] = $usersWithAccess;
        }

        return $this;
    }

    public function removeUsersWithAccess(User $usersWithAccess): self
    {
        if ($this->usersWithAccess->contains($usersWithAccess)) {
            $this->usersWithAccess->removeElement($usersWithAccess);
        }

        return $this;
    }

    public function getAllowsAds(): ?bool
    {
        return $this->allowsAds;
    }

    public function setAllowsAds(?bool $allowsAds): self
    {
        $this->allowsAds = $allowsAds;

        return $this;
    }
}
