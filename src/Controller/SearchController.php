<?php

namespace App\Controller;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController {

    /**
     * @Route("/video/search", name="app_video_search")
     */
    public function search(Request $request) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        dd($request);
        $filter = 'taki film';
//        dd($request);
        $videos = $this->getDoctrine()->getManager()->getRepository(Video::class)->findByFilter($filter);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'videos' => $videos
        ]);
    }
}
