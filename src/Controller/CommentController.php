<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentRate;
use App\Repository\VideoRepository;
use App\Services\UserGetter;
use App\Services\VideoManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @param string $video_hash
     * @param Request $request
     * @return RedirectResponse
     */
    public function add(string $video_hash, Request $request)
    {
        $message = $request->request->get('message');
        $video = $this->videoRepository->findOneBy(['hash' => $video_hash]);
        $user = $this->security->getUser();

        $video->comment($message, $user);

        $this->em->persist($video);
        $this->em->flush();

        return $this->redirectToRoute('app_video_watch', [
            'video_hash' => $video_hash
        ]);
    }

    /**
     * @Route("/comment/{id}", name="comment_reply")
     * @param Comment $comment
     * @param Request $request
     * @return RedirectResponse
     */
    public function reply(Comment $comment, Request $request)
    {
        $message = $request->request->get('message');

        $reply = new Comment($comment);
        $reply->setAuthorUsername($this->getUser()->getUsername());
        $reply->setContents($message);

        $this->em->persist($reply);
        $this->em->flush();

        return $this->redirectToRoute('app_video_watch', [
            'video_hash' => $comment->getVideo()->getHash()
        ]);
    }

    /**
     * @Route("/comment/{id}/rate/{grade}", name="app_comment_rate")
     * @param Comment $comment
     * @param bool $grade
     * @param UserGetter $userGetter
     * @return RedirectResponse
     */
    public function rate(Comment $comment, bool $grade, UserGetter $userGetter)
    {
        $user = $userGetter->get();
        $rate = $this->em->getRepository(CommentRate::class)->findBy([
            'author' => $user,
            'comment' => $comment
        ]);

        if (count($rate) > 0) {
            foreach ($rate as $item) {
                $this->em->remove($item);
            }
        }

        $comment->rate($grade, $userGetter->get());
        $this->em->persist($comment);
        $this->em->flush();

        return $this->redirectToRoute('app_video_watch', ['video_hash' => $comment->getVideo()->getHash()]);
    }
}
