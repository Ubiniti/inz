<?php

namespace App\Services\Uploader;

use App\Services\Uploader\Exception\FileFormatException;
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
    }

    public function saveAvatar(UploadedFile $file): string {
        $ext = $file->getClientOriginalExtension();

        if (!in_array($ext, self::ALLOWED_FORMATS)) {
            throw new FileFormatException();
        }

        return $this->binaryUploader->saveFile($file, self::AVATARS_DIR);
    }
}