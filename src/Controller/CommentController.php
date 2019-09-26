<?php

namespace App\Controller;

use App\Repository\VideoRepository;
use App\Services\VideoManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentController extends AbstractController
{
    /**
     * @var VideoManager
     */
    private $videoManager;
    /**
     * @var VideoRepository
     */
    private $videoRepository;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        VideoManager $videoManager,
        VideoRepository $videoRepository,
        EntityManagerInterface $em,
        Security $security
    )
    {
        $this->videoManager = $videoManager;
        $this->videoRepository = $videoRepository;
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @Route("/video/{video_hash}/comment", methods={"POST"}, name="add_comment")
     */
    public function add(string $video_hash, Request $request)
    {
        $message = $request->request->get('message');
        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);
        $user = $this->security->getUser();

        $video->comment($message, $user);

        $this->em->persist($video);
        $this->em->flush();

        return $this->redirectToRoute('video_watch',
            ['video_hash' => $video_hash]);
    }
}
