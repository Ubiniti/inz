<?php

namespace App\Entity;

use App\Dto\RegistrationDto;
use App\Services\Uploader\AvatarUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Length(
     *      min = 4,
     *      max = 30,
     *      minMessage = "Your username must be at least {{ limit }} characters long",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters"
     * )
     * @Assert\NotBlank
     * @Assert\Regex("/^[0-9A-Za-z_-]{4,30}$/", message = "The login '{{ value }}' is not a valid login.")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * * @Assert\Length(
     *      min = 8,
     *      max = 4096,
     *      minMessage = "Your password must be at least {{ limit }} characters long",
     *      maxMessage = "Your password cannot be longer than {{ limit }} characters"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="date")
     * @Assert\DateTime
     * @var string A "Y-m-d" formatted value
     */
    private $joinDate;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank
     */
    private $country;

    /**
     * @ORM\Column(type="date")
     * @Assert\DateTime
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Wallet", mappedBy="user", cascade={"persist", "remove"})
     */
    private $wallet;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Channel", mappedBy="user", cascade={"persist", "remove"})
     */
    private $channel;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Video", mappedBy="usersWithAccess")
     */
    private $paidForVideos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentRate", mappedBy="author")
     */
    private $commentRates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Advertisement", mappedBy="user", orphanRemoval=true)
     */
    private $advertisements;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="users")
     */
    private $preferredCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author", orphanRemoval=true)
     */
    private $comments;


    public function __construct()
    {
        $this->paidForVideos = new ArrayCollection();
        $this->commentRates = new ArrayCollection();
        $this->advertisements = new ArrayCollection();
        $this->preferredCategories = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public static function createFromDto(
        RegistrationDto $dto,
        UserPasswordEncoderInterface $encoder,
        AvatarUploader $uploader
    ): self {
        $self = new self();
        $self->username = $dto->getUsername();
        $self->password = $encoder->encodePassword($self, $dto->getPlainPassword());
        $self->email = $dto->getEmail();
        $self->country = $dto->getCountry();
        $self->birthday = $dto->getBirthday();
        $self->joinDate = new \DateTimeImmutable();
        if ($dto->getAvatar()) {
            $self->avatar = $uploader->saveAvatar($dto->getAvatar());
        }

        return $self;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getJoinDate(): string
    {
        return $this->joinDate;
    }

    public function setJoinDate(string $joinDate): self
    {
        $this->joinDate = $joinDate;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(Wallet $wallet): self
    {
        $this->wallet = $wallet;

        // set the owning side of the relation if necessary
        if ($this !== $wallet->getUser()) {
            $wallet->setUser($this);
        }

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(Channel $channel): self
    {
        $this->channel = $channel;

        // set the owning side of the relation if necessary
        if ($this !== $channel->getUser()) {
            $channel->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getPaidForVideos(): Collection
    {
        return $this->paidForVideos;
    }

    public function addPaidForVideo(Video $paidForVideo): self
    {
        if (!$this->paidForVideos->contains($paidForVideo)) {
            $this->paidForVideos[] = $paidForVideo;
            $paidForVideo->addUsersWithAccess($this);
        }

        return $this;
    }

    public function removePaidForVideo(Video $paidForVideo): self
    {
        if ($this->paidForVideos->contains($paidForVideo)) {
            $this->paidForVideos->removeElement($paidForVideo);
            $paidForVideo->removeUsersWithAccess($this);
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
            $commentRate->setAuthor($this);
        }

        return $this;
    }

    public function removeCommentRate(CommentRate $commentRate): self
    {
        if ($this->commentRates->contains($commentRate)) {
            $this->commentRates->removeElement($commentRate);
            // set the owning side to null (unless already changed)
            if ($commentRate->getAuthor() === $this) {
                $commentRate->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Advertisement[]
     */
    public function getAdvertisements(): Collection
    {
        return $this->advertisements;
    }

    public function addAdvertisement(Advertisement $advertisement): self
    {
        if (!$this->advertisements->contains($advertisement)) {
            $this->advertisements[] = $advertisement;
            $advertisement->setUser($this);
        }

        return $this;
    }

    public function removeAdvertisement(Advertisement $advertisement): self
    {
        if ($this->advertisements->contains($advertisement)) {
            $this->advertisements->removeElement($advertisement);
            // set the owning side to null (unless already changed)
            if ($advertisement->getUser() === $this) {
                $advertisement->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getPreferredCategories(): Collection
    {
        return $this->preferredCategories;
    }

    public function addPreferredCategory(Category $preferredCategory): self
    {
        if (!$this->preferredCategories->contains($preferredCategory)) {
            $this->preferredCategories[] = $preferredCategory;
        }

        return $this;
    }

    public function removePreferredCategory(Category $preferredCategory): self
    {
        if ($this->preferredCategories->contains($preferredCategory)) {
            $this->preferredCategories->removeElement($preferredCategory);
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
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }
}
