<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDto
{
    /**
     * @var string
     * @Assert\Regex(pattern="/^[A-Za-z\d_\.-]{3,20}$/")
     */
    private $username;

    /**
     * @var string
     * @Assert\Regex(
     *     message="Podane hasło nie spełnia wymagań",
     *     pattern="/^[A-Za-z\d_\.-]{3,20}$/"
     * )
     */
    private $plainPassword;

    /**
     * @var string
     * @Assert\Email(message="Podany email jest nieprawidłowy")
     */
    private $email;

    /**
     * @var string
     */
    private $country;

    /**
     * @var \DateTime
     * @Assert\DateTime(message="Podaj prawidłową datę urodzenia")
     */
    private $birthday;

    /**
     * @var ?UploadedFile
     * @Assert\Image()
     */
    private $avatar;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function getAvatar(): ?UploadedFile
    {
        return $this->avatar;
    }

    public function setAvatar(?UploadedFile $avatar): void
    {
        $this->avatar = $avatar;
    }
}