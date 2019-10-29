<?php

namespace App\Services\Uploader;

use App\Services\Uploader\Exception\FileFormatException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ChannelBannerUploader
{
    const ADS_DIR = 'channel';
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

    /**
     * @param UploadedFile $file
     * @return string
     * @throws Exception\PathsJoinException
     * @throws FileFormatException
     */
    public function saveContent(UploadedFile $file): string {
        return $this->binaryUploader->saveFile($file, self::ADS_DIR);
    }
}