<?php

namespace App\Services\Uploader;

use App\Services\Uploader\Exception\FileFormatException;
use App\Services\Uploader\Exception\PathsJoinException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class BinaryFileUploader
{
    const PUBLIC_DIR = '/public';
    const DEFAULT_UPLOAD_DIR = '/uploads';

    /**
     * @var array
     */
    private $allowedFormats;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->em = $em;
        $this->kernel = $kernel;
    }

    /**
     * @param string $extension
     * @param UploadedFile $file
     * @param string $directory
     * @return string Name of uploaded file excluding extension
     * @throws PathsJoinException
     */
    public function saveFile(UploadedFile $file, string $directory = ''): string {
        $tmpPath = $file->getPathname();

        $extension = $file->getClientOriginalExtension();

        if (!$this->isFormatAllowed($extension)) {
            throw new FileFormatException();
        }

        $hash = md5(uniqid());

        $uploadDirectory = $this->getUploadsPath($directory);

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $destination = $uploadDirectory . $hash . '.' . $extension;

        copy($tmpPath, $destination);

        return $hash;
    }

    /**
     * @param string $subDirectory
     * @return string
     * @throws PathsJoinException
     */
    private function getUploadsPath(string $subDirectory = ''): string
    {
        $uploadsPath = isset($_ENV['UPLOADS_DIR'])
            ? self::PUBLIC_DIR . $_ENV['UPLOADS_DIR']
            : self::PUBLIC_DIR . self::DEFAULT_UPLOAD_DIR;

        $projectDir = $this->kernel->getProjectDir();

        return $this->joinPaths([$projectDir, $uploadsPath, $subDirectory]);
    }

    public static function getUploadsRelativePath(string $subDirectory = '')
    {
        $uploadsPath = isset($_ENV['UPLOADS_DIR'])
            ? $_ENV['UPLOADS_DIR']
            : self::DEFAULT_UPLOAD_DIR;

        return self::joinPaths([$uploadsPath, $subDirectory]);
    }

    /**
     * @param array $paths
     * @param bool $followingSlash
     * @return string
     * @throws PathsJoinException
     */
    private static function joinPaths(array $paths, bool $followingSlash = true): string
    {
        $slash = $followingSlash ? DIRECTORY_SEPARATOR : '';

        if (count($paths) === 0) {
            throw new PathsJoinException();
        }

        $output = rtrim($paths[0], '\\/');

        foreach ($paths as $key => $path) {
            if ($path === '' || $key === 0) {
                continue;
            }

            $output .= DIRECTORY_SEPARATOR . trim($path, '\\/');
        }
        $output .= $slash;

        return $output;
    }

    public function getAllowedFormats(): array
    {
        return $this->allowedFormats;
    }

    public function setAllowedFormats(array $allowedFormats): self
    {
        $this->allowedFormats = $allowedFormats;

        return $this;
    }

    private function isFormatAllowed(string $extension)
    {
        return empty($this->allowedFormats) || in_array($extension, $this->allowedFormats);
    }
}