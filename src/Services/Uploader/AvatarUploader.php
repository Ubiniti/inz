<?php

namespace App\Services\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class AvatarUploader
{
    const AVATARS_DIR = 'avatars';
    const ALLOWED_FORMATS = ['jpg', 'png', 'gif', 'jpeg'];

    /**
     * @var BinaryFileUploader
     */
    private $binaryUploader;

    public function __construct(BinaryFileUploader $binaryUploader)
    {
        $this->binaryUploader = $binaryUploader;
        $this->binaryUploader->setAllowedFormats(self::ALLOWED_FORMATS);
    }

    public function saveAvatar(UploadedFile $file): string {
        return $this->binaryUploader->saveFile($file, self::AVATARS_DIR);
    }

    public static function getUploadsRelativePath(): string
    {
        return BinaryFileUploader::getUploadsRelativePath(self::AVATARS_DIR);
    }
}