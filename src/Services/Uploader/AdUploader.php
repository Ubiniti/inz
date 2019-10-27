<?php

namespace App\Services\Uploader;

use App\Services\Uploader\Exception\FileFormatException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdUploader
{
    const ADS_DIR = 'advertisements';
    const ALLOWED_FORMATS = ['jpg', 'png', 'gif', 'jpeg'];
    /**
     * @var BinaryFileUploader
     */
    private $binaryUploader;

    public function __construct(BinaryFileUploader $binaryUploader)
    {
        $this->binaryUploader = $binaryUploader;
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws Exception\PathsJoinException
     * @throws FileFormatException
     */
    public function saveContent(UploadedFile $file): string {
        $ext = $file->getClientOriginalExtension();

        if (!in_array($ext, self::ALLOWED_FORMATS)) {
            throw new FileFormatException();
        }

        return $this->binaryUploader->saveFile($file, self::ADS_DIR, 'jpg');
    }
}