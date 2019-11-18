<?php

namespace App\Twig;

use App\Services\Uploader\AvatarUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UploadedAssetsExtension extends AbstractExtension
{
    const DEFAULT_AVATAR = '/assets/default/avatar-person.png';

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

    public function getFunctions()
    {
        return [
            new TwigFunction('avatar', [$this, 'avatar']),
        ];
    }

    public function avatar(?string $hash)
    {
        if (!$hash) {
            return self::DEFAULT_AVATAR;
        }

        $path = AvatarUploader::getUploadsRelativePath() . $hash;

        return $path . '.' . $this->guessExtension($this->kernel->getProjectDir() . '/public' . $path);
    }

    private function guessExtension(string $path)
    {
        return pathinfo(glob($path.'*')[0], PATHINFO_EXTENSION);
    }
}