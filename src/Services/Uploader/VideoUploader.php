<?php

namespace App\Services\Uploader;

use App\Dto\VideoUploadFormDto;
use App\Entity\User;
use App\Services\UserGetter;
use App\Services\VideoEditor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class VideoUploader
{
    const DEFAULT_UPLOAD_DIR = '/uploads';
    const THUMBS_DIR = '/thumbs';
    const DEMO_DIR = 'demos';
    const PUBLIC_DIR = '/public';
    const THUMB_FORMAT = 'jpg';
    const THUMB_FRAME_TIME = 10.0;
    const DEMO_DURATION = 60;

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
    /**
     * @var VideoEditor
     */
    private $videoEditor;

    public function __construct(VideoEditor $videoEditor, UserGetter $userGetter, EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->uploadsPath = self::PUBLIC_DIR.$_ENV['UPLOADS_DIR'];
        $this->userGetter = $userGetter;
        $this->em = $em;
        $this->kernel = $kernel;
        $this->videoEditor = $videoEditor;
    }

    public function saveVideo(VideoUploadFormDto $dto)
    {
        $tmpPath = $dto->getFile()->getPathname();
        $ext = $dto->getFile()->getClientOriginalExtension();
        $hash = md5(uniqid());
        $uploadsPath = $this->getUploadsPath();
        $thumbnailsPath = $this->getThumbnailsDirPath();
        $demosPath = $this->getDemosDirectory();

        if (!is_dir($uploadsPath)) {
            mkdir($uploadsPath, 0777, true);
        }

        if (!is_dir($thumbnailsPath)) {
            mkdir($thumbnailsPath, 0777, true);
        }

        if (!is_dir($demosPath)) {
            mkdir($demosPath, 0777, true);
        }

        $thumbnailFilePath = $this->getThumbnailPath($hash);
        $thumbnailFile = $dto->getThumbnail();

        if ($thumbnailFile) {
            $thumbnailTmpPath = $thumbnailFile->getPathname();
            copy($thumbnailTmpPath, $thumbnailFilePath);
        } else {
            $this->videoEditor->saveFrame($tmpPath, $thumbnailFilePath, self::THUMB_FRAME_TIME);
        }

        $finalVideoPath = $this->getUploadedVideoPath($hash);
        copy($tmpPath, $finalVideoPath);
        $convertedVideoPath = null;

        if ($dto->hasDemo()) {
            $this->videoEditor->createDemo($tmpPath, self::DEMO_DURATION, $this->getDemoPath($hash));
        }

        $watermark = $this->getWatermarkPath();

        if ($dto->hasWatermark() && $watermark) {
            $this->videoEditor->addWatermark($finalVideoPath, $watermark, $finalVideoPath);
        }

        $duration = $this->videoEditor->getDuration($tmpPath);

        /** @var User $author */
        $author = $this->em->getRepository(User::class)->findOneBy([
            'username' => $this->userGetter->getUsername()
        ]);
        $video = $dto->createVideoSkeleton();
        $video
            ->setHash($hash)
            ->setDuration($duration)
            ->setAuthorUsername($this->userGetter->getUsername())
            ->setChannel($author->getChannel());

        $this->em->persist($video);
        $this->em->flush();
    }

    /**
     * @return string Uploads directory path with trailing slash
     */
    private function getUploadsPath(): string
    {
        $projectDir = $this->kernel->getProjectDir();
        $slash = substr($this->uploadsPath, -1) === '/' ? '' : '/';

        return $this->uploadsPath !== null
            ? $projectDir . $this->uploadsPath . $slash
            : self::DEFAULT_UPLOAD_DIR;
    }

    private function getUploadedVideoPath(string $hash)
    {
        return $this->getUploadsPath() . $hash . '.mp4';
    }

    private function getThumbnailPath(string $hash)
    {
        return $this->getThumbnailsDirPath() . $hash . '.' . self::THUMB_FORMAT;
    }

    private function getThumbnailsDirPath()
    {
        return $this->getUploadsPath() . self::THUMBS_DIR . '/';
    }

    private function getDemoPath(string $videoHash): string
    {
        return $this->getDemosDirectory() . $videoHash . '.mp4';
    }

    private function getDemosDirectory()
    {
        return $this->getUploadsPath() . self::DEMO_DIR . '/';
    }

    private function getWatermarkPath(): ?string
    {
        if (empty($_ENV['WATERMARK'])) {
            return null;
        }

        return $this->kernel->getProjectDir() . '/public' . $_ENV['WATERMARK'];
    }
}