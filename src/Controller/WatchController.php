<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Comment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WatchController extends AbstractController
{
    /**
     * @Route("/watch/{video_hash}", name="watch")
     */
    public function index($video_hash)
    {
        //id = random string generated during upload
        //get full path from DB entry with corresponding id
        //get title,hashtags,comments,etc. from DB
        //a folder with .mp4 and comments.txt for each video
        $entityManager = $this->getDoctrine()->getManager();
        $videoRepo = $entityManager->getRepository(Video::class);
        $commentRepo = $entityManager->getRepository(Comment::class);

        $video = $videoRepo->findOneBy(['hash' => $video_hash]);
        $views = $video->getViews();
        $video->setViews(++$views);

        $entityManager->persist($video);
        $entityManager->flush();

        $comments = $commentRepo->findByVideoHash($video_hash);
        $comments_data = array();
        for($i = 0; $i < count($comments); $i++)
        {
            $comments_data[$i] = $comments[$i]->toArray();
        }
        
        return $this->render('watch/index.html.twig', [
            'controller_name' => 'WatchController',
            'video' => $video->toArray(),
            'comments' => $comments_data
        ]);
    }
    
    
}
