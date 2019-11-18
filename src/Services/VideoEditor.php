<?php

namespace App\Services;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;

class VideoEditor
{
    /**
     * @var UserGetter
     */
    private $userGetter;

    public function __construct(UserGetter $userGetter)
    {
        $this->userGetter = $userGetter;
    }

    public function saveFrame(string $sourcePath, string $targetPath, float $time)
    {
        $ffmpeg = FFMpeg::create();
        $videoFile = $ffmpeg->open($sourcePath);

        $videoFile
            ->frame(TimeCode::fromSeconds($time))
            ->save($targetPath);
    }

    /**
     * @param string $sourcePath Path to original video
     * @param int $duration
     * @param string $targetPath Where to save demo
     * @return string Path to created demo temporary file
     */
    public function createDemo(string $sourcePath, int $duration, ?string $targetPath = null): string
    {
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($sourcePath);

        $fileName = md5(uniqid()).'.mp4';
        $tmpFilePath = sys_get_temp_dir().'/'.$fileName;

        $clip = $video->clip(\FFMpeg\Coordinate\TimeCode::fromSeconds(0), \FFMpeg\Coordinate\TimeCode::fromSeconds($duration));
        $clip->save(new \FFMpeg\Format\Video\X264('libmp3lame'), $tmpFilePath);

        if ($targetPath) {
            copy($tmpFilePath, $targetPath);
            unlink($tmpFilePath);

            return $targetPath;
        }

        return $tmpFilePath;
    }

    /**
     * @param string $videoPath
     * @param string|null $watermark Path to watermark file
     * @param string|null $targetPath Modified video file path
     * @return string|null Created video file path
     */
    public function addWatermark(string $videoPath, string $watermark, ?string $targetPath = null): ?string
    {
        $x = 0;
        $y = 0;

        if (!empty($_ENV['WATERMARK_X'])) {
            $x = $_ENV['WATERMARK_X'];
        }

        if (!empty($_ENV['WATERMARK_Y'])) {
            $y = $_ENV['WATERMARK_Y'];
        }

        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($videoPath);
        $video
            ->filters()
            ->watermark($watermark, array(
                'position' => 'absolute',
                'bottom' => $y,
                'right' => $x,
            ))
            ->synchronize();

        $fileName = md5(uniqid()).'.mp4';
        $tmpNewPath = sys_get_temp_dir().'/'.$fileName;

        $format = new \FFMpeg\Format\Video\X264('libmp3lame');
        $format->on('progress', function ($audio, $format, $percentage) {
            $this->saveConversionProgress($percentage);
        });

        $video
            ->save($format, $tmpNewPath);

        if ($targetPath) {
            copy($tmpNewPath, $targetPath);
            unlink($tmpNewPath);

            return $targetPath;
        }

        return $tmpNewPath;
    }

    private function saveConversionProgress($progress)
    {
        apcu_store('watermark_'.$this->userGetter->getUsername(), $progress);
    }

    public static function getConversionProgress(string $username): string
    {
        return apcu_fetch('watermark_'.$username);
    }

    public function getDuration(string $path)
    {
        return $this->getVideoFileProperty('duration', $path);
    }

    private function getVideoFileProperty(string $name, string $path)
    {
        $ffprobe = FFProbe::create();
        $format = $ffprobe
            ->format($path);

        $value = $format->get($name);

        return $value;
    }
}