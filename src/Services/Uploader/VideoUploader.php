<?php

namespace App\Services\Uploader;

use App\Dto\VideoUploadFormDto;
use App\Entity\Video;
use App\Services\UserGetter;
use Doctrine\ORM\EntityManagerInterface;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Symfony\Component\HttpKernel\KernelInterface;

class VideoUploader
{
    const DEFAULT_UPLOAD_DIR = '/uploads';
    const THUMBS_DIR = '/thumbs';
    const PUBLIC_DIR = '/public';
    const THUMB_FORMAT = 'jpg';
    const THUMB_FRAME_TIME = 10.0;

    private $uploadsPath;

    /**
     * @var UserGetter
     */
    private $userGetter;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(UserGetter $userGetter, EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->uploadsPath = self::PUBLIC_DIR.$_ENV['UPLOADS_DIR'];
        $this->userGetter = $userGetter;
        $this->em = $em;
        $this->kernel = $kernel;
    }

    public function saveVideo(VideoUploadFormDto $dto)
    {
        $title = $dto->getTitle();
        $tmpPath = $dto->getFile()->getPathname();
        $ext = $dto->getFile()->getClientOriginalExtension();
        $hash = md5(uniqid());
        $uploadsPath = $this->getUploadsPath();
        $thumbnailsPath = $this->getThumbnailsPath();

        if (!is_dir($uploadsPath)) {
            mkdir($uploadsPath, 0777, true);
        }

        if (!is_dir($thumbnailsPath)) {
            mkdir($thumbnailsPath, 0777, true);
        }

        $thumbnailFilePath = $thumbnailsPath . $hash . '.' . self::THUMB_FORMAT;
        $thumbnailFile = $dto->getThumbnail();

        if ($thumbnailFile) {
            $thumbnailTmpPath = $thumbnailFile->getPathname();
            $thumbnailExt = $thumbnailFile->getClientOriginalExtension();
            copy($thumbnailTmpPath, $thumbnailsPath . $hash . '.' .$thumbnailExt);
        } else {
            $this->saveFrameFromVideo($tmpPath, $thumbnailFilePath, self::THUMB_FRAME_TIME);
        }

        copy($tmpPath, $uploadsPath . $hash . '.' .$ext);

        $duration = $this->getVideoFileProperty('duration', $tmpPath);

        $video = new Video();
        $video
            ->setTitle($title)
            ->setHash($hash)
            ->setDuration($duration)
            ->setDescription($dto->getDescription())
            ->setAuthorUsername($this->userGetter->getUsername())
            // TODO: set category
            ->setCategory('');

        $this->em->persist($video);
        $this->em->flush();
    }

    private function getVideoFileProperty(string $name, string $path)
    {
        $ffprobe = FFProbe::create();
        $format = $ffprobe
            ->format($path);

        $value = $format->get($name);

        return $value;
    }

    private function saveFrameFromVideo(string $sourcePath, string $targetPath, float $time)
    {
        $ffmpeg = FFMpeg::create();
        $videoFile = $ffmpeg->open($sourcePath);

        $videoFile
            ->frame(TimeCode::fromSeconds($time))
            ->save($targetPath);
    }

    private function getUploadsPath()
    {
        $projectDir = $this->kernel->getProjectDir();
        $slash = substr($this->uploadsPath, -1) === '/' ? '' : '/';

        return $this->uploadsPath !== null
            ? $projectDir . $this->uploadsPath . $slash
            : self::DEFAULT_UPLOAD_DIR;
    }

    private function getThumbnailsPath()
    {
        return $this->getUploadsPath() . self::THUMBS_DIR . '/';
    }
}