<?php

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Video;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class VideoUploadFormDto
{
    /**
     * @var string
     * @Assert\NotBlank;
     */
    private $title;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var UploadedFile
     * @Assert\NotBlank;
     */
    private $file;

    /**
     * @var ?UploadedFile
     */
    private $thumbnail;

    /**
     * @var Category[]|Collection
     */
    private $categories;

    /**
     * @var bool
     */
    private $isPublic;

    /**
     * @var bool
     */
    private $watermark;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->isPublic = false;
        $this->watermark = false;
    }

    public function createVideoSkeleton(): Video
    {
        return (new Video())
            ->setTitle($this->title)
            ->setDescription($this->description)
            ->setCategories($this->categories)
            ->setIsPublic($this->isPublic);
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getThumbnail(): ?UploadedFile
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?UploadedFile $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return Category[]|Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Category[]|Collection $categories
     */
    public function setCategories(Collection $categories): void
    {
        $this->categories = $categories;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(?bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }

    public function hasWatermark(): bool
    {
        return $this->watermark;
    }

    public function setWatermark(bool $watermark): void
    {
        $this->watermark = $watermark;
    }
}