<?php

namespace App\Services\Uploader;

use App\Services\Uploader\Exception\PathsJoinException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class BinaryFileUploader
{
    const PUBLIC_DIR = '/public';
    const DEFAULT_UPLOAD_DIR = '/uploads';

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
     * @param string $destination Subdirectory in uploads
     * @return string Name of uploaded file excluding extension
     */
    public function saveFile(UploadedFile $file, string $directory = ''): string {
        $tmpPath = $file->getPathname();
        $ext = $file->getClientOriginalExtension();
        $hash = md5(uniqid());

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        $uploadDirectory = $this->getUploadsPath($directory);

        $destination = $uploadDirectory . $hash . $ext;

        copy($tmpPath, $destination);

        return $hash;
    }

    private function getUploadsPath(string $subDirectory = ''): string
    {
        $uploadsPath = isset($_ENV['UPLOADS_DIR'])
            ? self::PUBLIC_DIR . $_ENV['UPLOADS_DIR']
            : self::PUBLIC_DIR . self::DEFAULT_UPLOAD_DIR;

        $projectDir = $this->kernel->getProjectDir();

        return $this->joinPaths([$projectDir, $uploadsPath, $subDirectory]);
    }

    private function joinPaths(array $paths, bool $followingSlash = true): string
    {
        $slash = $followingSlash ? DIRECTORY_SEPARATOR : '';

        if (count($paths) === 0) {
            throw new PathsJoinException();
        }

        $output = rtrim($paths[0], ['\\', '/']);

        foreach ($paths as $path) {
            if ($path === '') {
                continue;
            }

            $output .= DIRECTORY_SEPARATOR . trim($path, ['\\', '/']);
        }

        $output .= $slash;

        return $output;
    }
}