<?php

namespace App\Controller;

use App\Entity\Comment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/watch/{video_hash}/comment", methods={"POST"}, name="add_comment")
     */
    public function add($video_hash, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $commentRepo = $entityManager->getRepository(Comment::class);

        $user = $this->getUser();

        $comment = new Comment();
        $comment->setContents($request->request->get('contents'));
        $comment->setAuthorUsername($user->getUsername());
        $comment->setAdded(new \DateTime());
        $comment->setVideoHash($video_hash);
        $comment->generateHash();

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('watch',
            ['video_hash' => $video_hash]);
    }
}
