<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\VideoRate;
use App\Entity\Comment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WatchController extends AbstractController
{
    /**
     * @Route("/watch/{video_hash}", name="watch")
     */
    public function index($video_hash)
    {
        $user = $this->getUser();
        
        $entityManager = $this->getDoctrine()->getManager();
        $videoRepo = $entityManager->getRepository(Video::class);
        $videoRateRepo = $entityManager->getRepository(VideoRate::class);
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

        $thumbs_up = $videoRateRepo->countRate($video_hash, VideoRate::UP);
        $thumbs_down = $videoRateRepo->countRate($video_hash, VideoRate::DOWN);
        
        $rate = NULL;

        if($user) {
            $videoRate = $videoRateRepo->findOneByViewer($video_hash, $user->getUsername());
            if($videoRate) {
                $rate = $videoRate->getRate();
            }
        }

        return $this->render('watch/index.html.twig', [
            'controller_name' => 'WatchController',
            'video' => $video->toArray(),
            'thumbs_up' => $thumbs_up,
            'thumbs_down' => $thumbs_down,
            'user_rate' => $rate,
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
     * @Route("/watch/{video_hash}/rate", methods={"GET"}, name="get_video_rate")
     */
    public function getRate($video_hash)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $videoRateRepo = $entityManager->getRepository(VideoRate::class);

        $countThumbsUp = $videoRateRepo->countRate($video_hash, VideoRate::UP);
        $countThumbsDown = $videoRateRepo->countRate($video_hash, VideoRate::DOWN);

        $rate = [
            "up" => $countThumbsUp,
            "down" => $countThumbsDown];

        $result = json_encode($rate);

        return new Response($result);
    }
    
}
