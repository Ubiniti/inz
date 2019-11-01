<?php

namespace App\Services\Uploader;

use App\Dto\VideoUploadFormDto;
use App\Entity\User;
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
        $convertedVideoPath = null;

        if ($dto->hasWatermark()) {
            $convertedVideoPath = $this->addWatermark($uploadsPath . $hash . '.' .$ext);
        }

        if ($convertedVideoPath) {
            unlink($uploadsPath . $hash . '.' .$ext);
            copy($convertedVideoPath, $uploadsPath . $hash . '.' .$ext);
        }

        $duration = $this->getVideoFileProperty('duration', $tmpPath);

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

    private function addWatermark(string $path): ?string
    {
        if (empty($_ENV['WATERMARK'])) {
            return null;
        }

        $watermarkPath = $this->kernel->getProjectDir() . '/public' . $_ENV['WATERMARK'];

        dump($path, $_ENV['WATERMARK'], file_exists($watermarkPath), $watermarkPath);

        $x = 0;
        $y = 0;

        if (!empty($_ENV['WATERMARK_X'])) {
            $x = $_ENV['WATERMARK_X'];
        }

        if (!empty($_ENV['WATERMARK_Y'])) {
            $y = $_ENV['WATERMARK_Y'];
        }

        $ffmpeg = FFMpeg::create();
        dump(file_exists($path), $path);
        $video = $ffmpeg->open($path);
        $video
            ->filters()
            ->watermark($watermarkPath, array(
                'position' => 'absolute',
                'bottom' => $y,
                'right' => $x,
            ))
            ->synchronize();

        $fileName = md5(uniqid()).'.mp4';
        $tmpNewPath = sys_get_temp_dir().'/'.$fileName;
        $video
            ->save(new \FFMpeg\Format\Video\X264('libmp3lame'), $tmpNewPath);

        return $tmpNewPath;
    }
}