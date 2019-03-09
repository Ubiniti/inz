<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Comment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\VideoRate;

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

    /**
     * @Route("/watch/{video_hash}/rate", methods={"POST"}, name="rate_video")
     */
    public function rate($video_hash, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $videoRateRepo = $entityManager->getRepository(VideoRate::class);

        $rate = $request->request->get('rate');

        $existingRate = $videoRateRepo->findOneByViewer($video_hash, $user->getUsername());

        if($existingRate)
        {
            $existingRate->setRate($rate);
            $entityManager->persist($existingRate);
        }
        else
        {
            $videoRate = new VideoRate();
            $videoRate->setVideoHash($video_hash);
            $videoRate->setViewerUsername($user->getUsername());
            $videoRate->setRate($rate);
            $entityManager->persist($videoRate);
        }

        $entityManager->flush();

        return $this->redirectToRoute('get_video_rate', [
            'video_hash' => $video_hash,
            'positive' => $rate
        ]);
    }

    /**
     * @Route("/watch/{video_hash}/rate/{positive}", methods={"GET"}, name="get_video_rate")
     */
    public function getRate($video_hash, $positive)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $videoRateRepo = $entityManager->getRepository(VideoRate::class);

        $count = $videoRateRepo->countRate($video_hash, $positive);

        return new Response($count);
    }
    
}
