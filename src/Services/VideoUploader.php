<?php

namespace App\Services;

use App\Dto\VideoUploadFormDto;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Symfony\Component\HttpKernel\KernelInterface;

class VideoUploader
{
    const DEFAULT_PATH = '/public/uploads/';
    const THUMB_FORMAT = 'jpg';
    const THUMB_FRAME_TIME = '10.0';

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
        $this->uploadsPath = $_ENV['UPLOADS_FOLDER'];
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
        $thumbnailsPath = $this->getTemplatesPath();

        if (!is_dir($uploadsPath)) {
            mkdir($uploadsPath);
        }

        if (!is_dir($thumbnailsPath)) {
            mkdir($thumbnailsPath);
        }

        copy($tmpPath, $uploadsPath . $hash . '.' .$ext);

        $ffprobe = FFProbe::create();
        $format = $ffprobe
            ->format($tmpPath);

        $duration = $format->get('duration');

        $ffmpeg = FFMpeg::create();
        $videoFile = $ffmpeg->open($tmpPath);

        $thumbFilePath = $thumbnailsPath . $hash . '.' . self::THUMB_FORMAT;
        $videoFile
            ->frame(TimeCode::fromSeconds(self::THUMB_FRAME_TIME))
            ->save($thumbFilePath);

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

    private function getUploadsPath()
    {
        $projectDir = $this->kernel->getProjectDir();
        $slash = substr($this->uploadsPath, -1) === '/' ? '' : '/';

        return $this->uploadsPath !== null ? $projectDir . $this->uploadsPath . $slash : self::DEFAULT_PATH;
    }

    private function getTemplatesPath()
    {
        return $this->getUploadsPath() . 'thumbs/';
    }
}