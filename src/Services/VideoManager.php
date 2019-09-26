<?php

namespace App\Services;

use App\Entity\Comment;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class VideoManager
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public function comment(string $message, Video $video)
    {
        $user = $this->security->getUser();

        $comment = (new Comment())
            ->setContents($message)
            ->setAuthorUsername($user->getUsername())
            ->setAdded(new \DateTime())
            ->setVideo($video);

        $this->em->persist($comment);
        $this->em->flush();
    }

    public function incrementViews(Video $video)
    {
        $views = $video->getViews();
        $video->setViews(++$views);

        $this->em->persist($video);
        $this->em->flush();
    }
}