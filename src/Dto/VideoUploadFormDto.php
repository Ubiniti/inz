<?php

namespace App\Dto;

use App\Entity\Category;
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
     * @var Category
     */
    private $categories;

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
     * @return Category
     */
    public function getCategories(): ?Category
    {
        return $this->categories;
    }

    /**
     * @param Category $categories
     */
    public function setCategories(Category $categories): void
    {
        $this->categories = $categories;
    }
}